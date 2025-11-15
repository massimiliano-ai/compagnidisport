#!/bin/bash
#
# Script to refactor terminology from Travel to Sport
# Run this from the plugin directory
#

PLUGIN_DIR="/home/user/compagnidisport/plugins/compagni-di-viaggi"

echo "==================================="
echo "  Refactoring Terminology"
echo "  Travel → Activity/Sport"
echo "==================================="
echo ""

cd "$PLUGIN_DIR"

echo "1. Updating PHP files in includes/ directory..."

# Function to replace in file
replace_in_file() {
    local file="$1"

    # Skip if file doesn't exist
    [ ! -f "$file" ] && return

    # Create backup
    cp "$file" "$file.bak"

    # Perform replacements (case-sensitive, preserving variable names)
    sed -i \
        -e "s/travel_id/activity_id/g" \
        -e "s/travel_participants/activity_participants/g" \
        -e "s/travel_group_messages/activity_group_messages/g" \
        -e "s/'viaggio'/'attivita'/g" \
        -e 's/"viaggio"/"attivita"/g' \
        -e "s/post_type = 'viaggio'/post_type = 'attivita'/g" \
        -e 's/post_type == "viaggio"/post_type == "attivita"/g' \
        -e "s/tipo_viaggio/tipo_sport/g" \
        -e "s/destinazione/luogo/g" \
        -e "s/Viaggio/Attività/g" \
        -e "s/viaggio/attività/g" \
        -e "s/Viaggi/Attività/g" \
        -e "s/viaggi/attività/g" \
        -e "s/compagni-di-viaggi/compagni-di-sport/g" \
        -e "s/Compagni di Viaggi/Compagni di Sport/g" \
        "$file"

    echo "  ✓ Updated: $(basename $file)"
}

# Update core classes
for file in includes/class-*.php; do
    [ -f "$file" ] && replace_in_file "$file"
done

# Update API classes
for file in includes/api/class-*.php; do
    [ -f "$file" ] && replace_in_file "$file"
done

# Update AJAX handlers
for file in includes/ajax/class-*.php; do
    [ -f "$file" ] && replace_in_file "$file"
done

# Update admin classes
for file in admin/class-*.php; do
    [ -f "$file" ] && replace_in_file "$file"
done

echo ""
echo "2. Renaming class files..."

# Rename travel-specific files
[ -f "includes/class-travel-stories.php" ] && mv "includes/class-travel-stories.php" "includes/class-sport-stories.php"
[ -f "includes/class-travel-gallery.php" ] && mv "includes/class-travel-gallery.php" "includes/class-activity-gallery.php"
[ -f "includes/class-travel-maps.php" ] && mv "includes/class-travel-maps.php" "includes/class-activity-maps.php"
[ -f "includes/class-travel-moderation.php" ] && mv "includes/class-travel-moderation.php" "includes/class-activity-moderation.php"

echo "  ✓ Renamed class files"

echo ""
echo "3. Cleaning up backup files..."
find . -name "*.bak" -type f -delete
echo "  ✓ Backup files removed"

echo ""
echo "==================================="
echo "  Refactoring Complete!"
echo "==================================="
echo ""
echo "Modified files in:"
echo "  - includes/"
echo "  - includes/api/"
echo "  - includes/ajax/"
echo "  - admin/"
echo ""
echo "Next: Update template files in theme directory"
