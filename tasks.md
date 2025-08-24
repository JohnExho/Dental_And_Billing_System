# 🐳 Docker TODO List

## 🏗 Build & Image
- [ ] Split build into multi-stage (Composer, Node, PHP-FPM) ✅
- [ ] Avoid heavy layers (remove apt cache, temp files)
- [ ] Add PHP Opcache extension for performance
- [ ] Add custom `php.ini` for production tuning

## ⚙️ Containers
- [ ] Create `entrypoint.sh` to:
  - [ ] Run `php artisan migrate --force`
  - [ ] Clear and cache config/routes/views on container start
- [ ] Add proper health checks for `app` and `db`
- [ ] Ensure `nginx` reloads smoothly on changes

## 🔗 Networking
- [ ] Confirm `nginx -> php-fpm` (laravel_app:9000) connection is stable
- [ ] Optimize Nginx config for Laravel
- [ ] Configure DB with named volume for persistence ✅

## 📂 Volumes & Files
- [ ] Mount only what’s necessary (avoid mounting entire project in prod)
- [ ] Add volume for logs (`storage/logs`)
- [ ] Add volume for database (`dbdata`) ✅

## 🧪 Development vs Production
- [ ] Create `docker-compose.override.yml` for dev (with volumes, hot reload)
- [ ] Keep production build slim (no dev deps, no node_modules/vendor mounts)
- [ ] Add `.env.docker` for container-specific settings

## 🚀 Deployment Ready
- [ ] Add SSL support (via reverse proxy like Traefik or Nginx with certbot)
- [ ] Add resource limits in docker-compose (memory, cpu)
- [ ] Prepare Docker Compose profiles (dev, prod)
- [ ] Test scaling `app` container (multiple PHP-FPM workers)
