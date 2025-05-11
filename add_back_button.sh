#!/bin/bash
BASE_DIR="/home/yeaksa/Downloads/Software/mekheav9/Modules/MiniReportB1/Resources/views/MiniReportB1/StandardReport"
find "$BASE_DIR" -name "*.blade.php" | grep -v "/partials/" | while read file; do
  if ! grep -q "@include('minireportb1::MiniReportB1.components.back_to_dashboard_button')" "$file"; then
    echo "Adding back button to $file"
    sed -i '/@include(.*linkforinclude.*)/a @include(\'minireportb1::MiniReportB1.components.back_to_dashboard_button\')' ""
    sed -i "/<div class=\"arrow\" id=\"goBackButton\"><\/div>/d" "$file"
  fi
done
echo "Completed adding back buttons to all reports"
