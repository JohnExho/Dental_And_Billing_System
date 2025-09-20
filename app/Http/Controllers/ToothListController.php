<?php

namespace App\Http\Controllers;

use App\Models\Logs;
use App\Models\ToothList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ToothListController extends Controller
{
    protected $guard;

    public function __construct()
    {
        $this->guard = Auth::guard('account');
    }

    public function index()
    {
        $teeth = ToothList::latest()
            ->paginate(8);

        return view('pages.teeth.index', compact('teeth'));
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'number' => 'required|integer|min:1|max:32|unique:tooth_list,number',
            'price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return back()->with('error', $validator->errors()->first());
        }

        $tooth = ToothList::create([
            'name' => $request->name,
            'name_hash' => hash('sha256', strtolower($request->name)),
            'number' => $request->number,
            'price' => $request->price,
        ]);
        $authAccount = $this->guard->user();
        Logs::record(
            $authAccount, // actor (logged-in user)
            null,
            null,
            null,
            'create',
            'Teeth',
            'User created a tooth',
            'Tooth: '.$tooth->name.' (#'.$tooth->number.') Price: '.$tooth->price,
            $request->ip(),
            $request->userAgent()
        );

        return redirect()->route('teeth')->with('success', 'Tooth created successfully.');
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tooth_list_id' => 'required|exists:tooth_list,tooth_list_id',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'number' => [
                'required',
                'integer',
                'min:1',
                'max:32',
                Rule::unique('tooth_list', 'number')->ignore($request->tooth_list_id, 'tooth_list_id'),
            ],
        ], [
            'number.unique' => 'This tooth number is already assigned.',
        ]);

        if ($validator->fails()) {
            return back()->with('error', $validator->errors()->first());
        }

        // Find the tooth by ID
        $tooth = ToothList::findOrFail($request->tooth_list_id);

        // Update its values
        $tooth->update([
            'name' => $request->name,
            'name_hash' => hash('sha256', strtolower($request->name)),
            'number' => $request->number,
            'price' => $request->price,
        ]);

        $authAccount = $this->guard->user();

        // Log the update action
        Logs::record(
            $authAccount,
            null,
            null,
            null,
            'update',
            'Teeth',
            'User updated a tooth',
            'Tooth: '.$tooth->name.' (#'.$tooth->number.') . Price: '.$tooth->price,
            $request->ip(),
            $request->userAgent()
        );

        return redirect()
            ->route('teeth')
            ->with('success', 'Tooth updated successfully.');
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'tooth_list_id' => 'required|exists:tooth_list,tooth_list_id',
            'password' => 'required',
        ]);

        $deletor = Auth::guard('account')->user();

        // Check if the password matches the current user's password
        if (! Hash::check($request->password, $deletor->password)) {
            return back()->with('error', 'The password is incorrect.');
        }

        $toothList = ToothList::findOrFail($request->tooth_list_id);

        return DB::transaction(function () use ($toothList, $deletor, $request) {
            $toothList->delete();

            // Logging
            Logs::record(
                $deletor,
                null,
                null,
                null,
                'delete',
                'Teeth',
                'User deleted a tooth',
                'Tooth: '.$toothList->name.' (#'.$toothList->number.')',
                $request->ip(),
                $request->userAgent()
            );

            return redirect()
                ->route('teeth')
                ->with('success', 'Tooth deleted successfully.');
        });
    }
}
