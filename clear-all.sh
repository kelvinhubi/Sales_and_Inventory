#!/bin/bash

# Text colors
GREEN='\033[0;32m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}Clearing Laravel Cache and Settings...${NC}"
echo

echo -e "${GREEN}Clearing Application Cache...${NC}"
php artisan cache:clear
echo

echo -e "${GREEN}Clearing Route Cache...${NC}"
php artisan route:clear
echo

echo -e "${GREEN}Clearing Configuration Cache...${NC}"
php artisan config:clear
echo

echo -e "${GREEN}Clearing Compiled Views...${NC}"
php artisan view:clear
echo

echo -e "${GREEN}Clearing Event Cache...${NC}"
php artisan event:clear
echo

echo -e "${GREEN}Optimizing Composer Autoload...${NC}"
composer dump-autoload
echo

echo -e "${GREEN}Clearing Session Data...${NC}"
# Delete session files from storage
rm -f storage/framework/sessions/*
echo

echo -e "${GREEN}Clearing Browser Cache Headers...${NC}"
php artisan cache:clear-headers
echo

echo -e "${GREEN}Re-generating IDE Helper Files...${NC}"
php artisan ide-helper:generate
php artisan ide-helper:models -N
echo

echo -e "${BLUE}All cache cleared successfully!${NC}"
echo -e "${BLUE}==============================${NC}"
echo
read -p "Press any key to continue..."
