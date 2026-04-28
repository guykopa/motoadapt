project   = "motoadapt"
author    = "guykopa"
release   = "1.0.0"
copyright = "2026, guykopa"

extensions = []

templates_path  = ["_templates"]
exclude_patterns = ["_build"]

html_theme       = "sphinx_rtd_theme"
html_static_path = ["_static"]
html_title       = "motoadapt — Adaptive Rehabilitation Engine"

html_theme_options = {
    "navigation_depth": 3,
    "titles_only": False,
    "collapse_navigation": False,
    "sticky_navigation": True,
}
