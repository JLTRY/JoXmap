VERSION = "1.0.0"
VERSION2 = $(shell echo $(VERSION)|sed 's/ /-/g')
ZIPFILE = joxmap-$(VERSION2).zip
PACKAGES = packages

# Only set DATE if you need to force the date.  
# (Otherwise it uses the current date.)
# DATE = "February 19, 2011"

all: links parts $(ZIPFILE)

INSTALLS = xmap_plugin \
		xmap_component

EXTRAS = 

NAMES = $(INSTALLS) $(EXTRAS)

ZIPS = $(NAMES:=.zip)

ZIPIGNORES = -x "*.git*" -x "*.svn*"

parts: $(ZIPS)

links:
	rm xmap_component/admin xmap_component/front
	ln -srf administrator/components/com_xmap xmap_component/admin
	ln -srf components/com_xmap xmap_component/front
	ln -srf plugins/xmap xmap_plugin

%.zip:
	@echo "-------------------------------------------------------"
	@echo "Creating zip file for: $*"
	@rm -f $@
	@(cd $*; zip -r ../$@ * $(ZIPIGNORES))

$(ZIPFILE): $(ZIPS)
	@echo "-------------------------------------------------------"
	@echo "Creating extension zip file: $(ZIPFILE)"
	@mv $(INSTALLS:=.zip) packages/
	@(cd $(PACKAGES); zip -r ../$@ * $(ZIPIGNORES))
	@echo "-------------------------------------------------------"
	@echo "Finished creating package $(ZIPFILE)."

