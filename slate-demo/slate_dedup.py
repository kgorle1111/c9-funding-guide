"""Consolidate duplicate prospective-student records — a stand-in for the
Slate CRM record-consolidation task in the UCSC Admissions Web Designer role.

Match rules (either one links two records into the same student):
  1. Same email, case-insensitive.
  2. Same normalized name (lowercased, punctuation stripped) + same date of birth.

Survivorship: per field, non-empty beats empty; among non-empty values the
record with the newest `updated_at` wins. The merged record keeps every
source record id in `merged_from` so the consolidation is auditable.

Usage:
    python3 slate_dedup.py students.csv merged.csv   # writes merged CSV + prints report
    python3 slate_dedup.py --demo                    # runs on bundled sample data
"""

from __future__ import annotations

import csv
import re
import sys
from datetime import date
from pathlib import Path

FIELDS = ["id", "first_name", "last_name", "email", "dob", "phone",
          "high_school", "intended_major", "updated_at"]


def norm_name(first: str, last: str) -> str:
    return re.sub(r"[^a-z]", "", (first + last).lower())


def keys(row: dict[str, str]) -> list[str]:
    """Every identity key a record asserts; sharing any one key links records."""
    out = []
    if row["email"].strip():
        out.append("email:" + row["email"].strip().lower())
    if row["dob"].strip() and norm_name(row["first_name"], row["last_name"]):
        out.append("namedob:" + norm_name(row["first_name"], row["last_name"]) + "|" + row["dob"].strip())
    return out


def cluster(rows: list[dict[str, str]]) -> list[list[dict[str, str]]]:
    """Union-find over identity keys → clusters of records for the same student."""
    parent: dict[int, int] = {i: i for i in range(len(rows))}

    def find(i: int) -> int:
        while parent[i] != i:
            parent[i] = parent[parent[i]]
            i = parent[i]
        return i

    by_key: dict[str, int] = {}
    for i, row in enumerate(rows):
        for k in keys(row):
            if k in by_key:
                parent[find(i)] = find(by_key[k])
            else:
                by_key[k] = i

    clusters: dict[int, list[dict[str, str]]] = {}
    for i, row in enumerate(rows):
        clusters.setdefault(find(i), []).append(row)
    return list(clusters.values())


def merge(records: list[dict[str, str]]) -> dict[str, str]:
    """Newest non-empty value per field wins; empty never overwrites non-empty."""
    ordered = sorted(records, key=lambda r: r["updated_at"])  # ISO dates sort lexically
    out = {f: "" for f in FIELDS}
    for rec in ordered:  # oldest → newest, so newest non-empty lands last
        for f in FIELDS:
            if rec[f].strip():
                out[f] = rec[f].strip()
    out["merged_from"] = ";".join(r["id"] for r in ordered)
    return out


def run(in_path: Path, out_path: Path) -> tuple[int, int]:
    with in_path.open(newline="") as fh:
        rows = list(csv.DictReader(fh))
    merged = [merge(c) for c in cluster(rows)]
    merged.sort(key=lambda r: (r["last_name"].lower(), r["first_name"].lower()))
    with out_path.open("w", newline="") as fh:
        writer = csv.DictWriter(fh, fieldnames=FIELDS + ["merged_from"])
        writer.writeheader()
        writer.writerows(merged)
    return len(rows), len(merged)


def main() -> None:
    if sys.argv[1:] == ["--demo"]:
        here = Path(__file__).parent
        n_in, n_out = run(here / "sample_students.csv", here / "merged_students.csv")
    elif len(sys.argv) == 3:
        n_in, n_out = run(Path(sys.argv[1]), Path(sys.argv[2]))
    else:
        sys.exit(__doc__)
    print(f"{n_in} records in -> {n_out} consolidated students "
          f"({n_in - n_out} duplicates merged) on {date.today().isoformat()}")


if __name__ == "__main__":
    main()
