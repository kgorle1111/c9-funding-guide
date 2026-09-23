"""Run: python3 test_slate_dedup.py (stdlib only, no pytest needed in this PHP repo)."""

from slate_dedup import cluster, merge


def rec(**kw):
    base = {f: "" for f in ["id", "first_name", "last_name", "email", "dob",
                            "phone", "high_school", "intended_major", "updated_at"]}
    base.update(kw)
    return base


def test_email_match_case_insensitive():
    rows = [rec(id="a", email="X@y.com", updated_at="2026-01-01"),
            rec(id="b", email="x@Y.com", updated_at="2026-02-01")]
    assert len(cluster(rows)) == 1


def test_name_dob_match_without_email():
    rows = [rec(id="a", first_name="Jo", last_name="Lee", dob="2008-07-02", updated_at="2026-01-01"),
            rec(id="b", first_name="jo", last_name="LEE", dob="2008-07-02", updated_at="2026-02-01")]
    assert len(cluster(rows)) == 1


def test_no_false_merge_on_name_alone():
    rows = [rec(id="a", first_name="Jo", last_name="Lee", dob="2008-07-02", email="jo1@x.com", updated_at="2026-01-01"),
            rec(id="b", first_name="Jo", last_name="Lee", dob="2009-01-01", email="jo2@x.com", updated_at="2026-01-01")]
    assert len(cluster(rows)) == 2


def test_transitive_link():
    # a~b share email, b~c share name+dob -> all three are one student
    rows = [rec(id="a", email="m@x.com", updated_at="2026-01-01"),
            rec(id="b", email="M@x.com", first_name="Maya", last_name="F", dob="2008-03-14", updated_at="2026-02-01"),
            rec(id="c", first_name="maya", last_name="f", dob="2008-03-14", updated_at="2026-03-01")]
    assert len(cluster(rows)) == 1


def test_survivorship_newest_nonempty_wins():
    rows = [rec(id="a", email="m@x.com", phone="111", intended_major="Biology", updated_at="2026-01-01"),
            rec(id="b", email="m@x.com", phone="", intended_major="Marine Biology", updated_at="2026-04-01")]
    m = merge(rows)
    assert m["phone"] == "111"                      # empty never overwrites
    assert m["intended_major"] == "Marine Biology"  # newest non-empty wins
    assert m["merged_from"] == "a;b"


if __name__ == "__main__":
    for name, fn in sorted(globals().items()):
        if name.startswith("test_"):
            fn()
            print(f"PASS {name}")
    print("all tests passed")
