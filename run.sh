#!/bin/bash
set -e

OUTPUT_FILE="/tmp/api-client-test-$(date +%Y%m%d-%H%M%S).log"

echo "Building Docker image..."
docker-compose build php

echo "Running tests... Output will be saved to $OUTPUT_FILE"

docker-compose run --rm php vendor/bin/phpunit "$@" 2>&1 | tee "$OUTPUT_FILE"

echo ""
echo "Test output saved to: $OUTPUT_FILE"
