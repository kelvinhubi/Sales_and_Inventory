@echo off
echo Clearing Laravel Cache and Settings...
echo.

echo Clearing Application Cache...
php artisan cache:clear
echo.

echo Clearing Route Cache...
php artisan route:clear
echo.

echo Clearing Configuration Cache...
php artisan config:clear
echo.

echo Clearing Compiled Views...
php artisan view:clear
echo.

echo Clearing Event Cache...
php artisan event:clear
echo.

echo Optimizing Composer Autoload...
composer dump-autoload
echo.

echo Clearing Session Data...
@REM Delete session files from storage
del /q /s "storage\framework\sessions\*"
echo.

echo Clearing Browser Cache Headers...
php artisan cache:clear-headers
echo.

echo Re-generating IDE Helper Files...
php artisan ide-helper:generate
php artisan ide-helper:models -N
echo.

echo All cache cleared successfully!
echo ==============================
echo.
pause
