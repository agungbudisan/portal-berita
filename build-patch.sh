#!/bin/bash
set -e

# Tampilkan versi node
echo "Node version:"
node -v

# Tampilkan versi npm
echo "NPM version:"
npm -v

# Siapkan patch untuk Rollup
echo "Patching rollup..."
mkdir -p node_modules/@rollup
touch node_modules/@rollup/rollup-linux-x64-gnu.js

# Install dependencies dengan flag spesifik
echo "Installing dependencies..."
npm install --no-optional --no-audit --no-fund --prefer-offline

# Build assets
echo "Building assets..."
npm run build

echo "Build completed!"
