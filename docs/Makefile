SPHINXBUILD  = ../.sphinx-venv/bin/sphinx-build
SOURCEDIR    = source
BUILDDIR     = _build

.PHONY: help html clean

help:
	@echo "Usage:"
	@echo "  make html    Build HTML documentation"
	@echo "  make clean   Remove build output"

html:
	$(SPHINXBUILD) -b html $(SOURCEDIR) $(BUILDDIR)/html
	@echo "Build finished. Open $(BUILDDIR)/html/index.html"

clean:
	rm -rf $(BUILDDIR)
