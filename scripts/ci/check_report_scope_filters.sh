#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
MODULE_DIR="${ROOT_DIR}/app/Reports/Modules"

if [[ ! -d "${MODULE_DIR}" ]]; then
  echo "Module directory not found: ${MODULE_DIR}"
  exit 1
fi

failures=0

while IFS= read -r file; do
  name="$(basename "${file}")"

  case "${name}" in
    BaseReport.php|SampleVillageProfileReport.php)
      continue
      ;;
  esac

  if ! rg -q "function scope\\(\\): string" "${file}"; then
    echo "FAIL ${name}: missing scope() contract implementation"
    failures=$((failures + 1))
    continue
  fi

  if ! rg -q "return 'desa';|return 'kecamatan';" "${file}"; then
    echo "FAIL ${name}: scope() must return 'desa' or 'kecamatan'"
    failures=$((failures + 1))
  fi

  if rg -q -- "->scoped\\(" "${file}"; then
    continue
  fi

  has_level_filter=0
  has_area_filter=0

  if rg -q "where\\('level'|where\\('a\\.level'|where\\('h\\.level'" "${file}"; then
    has_level_filter=1
  fi

  if rg -q "where\\('area_id'|where\\('a\\.parent_id'|where\\('h\\.area_id'" "${file}"; then
    has_area_filter=1
  fi

  if [[ ${has_level_filter} -eq 0 || ${has_area_filter} -eq 0 ]]; then
    echo "FAIL ${name}: missing explicit area filter guard (level + area_id)"
    failures=$((failures + 1))
  fi
done < <(find "${MODULE_DIR}" -maxdepth 1 -type f -name "*.php" | sort)

if [[ ${failures} -gt 0 ]]; then
  echo "Report scope filter check failed with ${failures} violation(s)."
  exit 1
fi

echo "Report scope filter check passed."
