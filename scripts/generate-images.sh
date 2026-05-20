#!/bin/bash

##############################################################################
# Image Optimization Script for HomePage.vue
# 
# Converts images to AVIF format in 3 responsive sizes
# Usage: ./generate-images.sh
##############################################################################

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

# Directories
IMAGES_DIR="public/images"

##############################################################################
# Helper Functions
##############################################################################

print_header() {
    echo -e "\n${BLUE}══════════════════════════════════════════════════════════${NC}"
    echo -e "${BLUE}$1${NC}"
    echo -e "${BLUE}══════════════════════════════════════════════════════════${NC}\n"
}

print_step() {
    echo -e "${YELLOW}→${NC} $1"
}

print_success() {
    echo -e "${GREEN}✓${NC} $1"
}

print_error() {
    echo -e "${RED}✗${NC} $1"
}

check_command() {
    if ! command -v "$1" &> /dev/null; then
        print_error "$1 not found. Please install it and try again."
        exit 1
    fi
}

##############################################################################
# Generate AVIF Images
##############################################################################

generate_avif() {
    local image_name=$1
    
    # Look for source image in public/images (jpeg or png)
    local source_file=""
    if [[ -f "$IMAGES_DIR/$image_name.jpeg" ]]; then
        source_file="$IMAGES_DIR/$image_name.jpeg"
    elif [[ -f "$IMAGES_DIR/$image_name.jpg" ]]; then
        source_file="$IMAGES_DIR/$image_name.jpg"
    elif [[ -f "$IMAGES_DIR/$image_name.png" ]]; then
        source_file="$IMAGES_DIR/$image_name.png"
    else
        print_error "No source found for: $image_name (looked for .jpeg, .jpg, .png)"
        return 1
    fi
    
    print_header "Converting: $image_name to AVIF"
    print_step "Source: $source_file"
    
    # Define 3 sizes: mobile, tablet, desktop
    local -a sizes=("mobile:360" "tablet:600" "desktop:800")
    local quality=80
    
    for size_spec in "${sizes[@]}"; do
        IFS=':' read -r size_name width <<< "$size_spec"
        local output_file="$IMAGES_DIR/${image_name}-${size_name}.avif"
        
        print_step "Generating $image_name-$size_name (${width}px)..."
        
        convert "$source_file" \
            -resize "${width}>" \
            -quality "$quality" \
            "$output_file"
        
        local file_size=$(du -h "$output_file" | cut -f1)
        print_success "  $image_name-$size_name.avif ($file_size)"
    done
    
    print_success "Finished: $image_name\n"
}

##############################################################################
# Main Script
##############################################################################

print_header "🖼️  AVIF Image Converter"

# Check dependencies
print_step "Checking dependencies..."
check_command "convert"
print_success "ImageMagick found\n"

# Check if images directory exists
if [[ ! -d "$IMAGES_DIR" ]]; then
    print_error "Images directory not found: $IMAGES_DIR"
    exit 1
fi

# Generate images
generate_avif "grup2" || true
generate_avif "logo" || true
generate_avif "nosaltres1" || true

# Summary
print_header "📊 Complete"

total_size=$(du -sh "$IMAGES_DIR" | cut -f1)
file_count=$(find "$IMAGES_DIR" -maxdepth 1 -name "*.avif" | wc -l)

echo -e "AVIF files in $IMAGES_DIR: ${GREEN}$file_count${NC}"
echo -e "Total size: ${GREEN}$total_size${NC}\n"

print_success "✓ Conversion complete!"
echo -e "\nNext: ${BLUE}vendor/bin/sail npm run build${NC}\n"
