VERSION = "1.0.0"
VERSION2 = $(shell echo $(VERSION)|sed 's/ /-/g')
ZIPFILE = joxmap-$(VERSION2).zip
PACKAGES = packages

# Only set DATE if you need to force the date.  
# (Otherwise it uses the current date.)
# DATE = "February 19, 2011"

all: $(ZIPFILE)

INSTALLS = joxmap_plugin \
		joxmap_component

NAMES = $(INSTALLS)

ZIPS = $(NAMES:=.zip)

ZIPIGNORES = -x "*.git*" -x "*.svn*"

PWD = $(pwd)

parts: $(ZIPS)

COMPONENT_SRC = administrator components media joxmap.xml
PLUGIN_SRC = plugins/joxmap/com_content


joxmap_component.zip: $(COMPONENT_SRC)
	@echo "-------------------------------------------------------"
	@echo "Creating zip file for: $*"
	@rm -f $@
	@(zip -r $@ $^  $(ZIPIGNORES))

joxmap_plugin.zip: $(PLUGIN_SRC)
	@echo "-------------------------------------------------------"
	@echo "Creating zip file for: $*"
	@rm -f $@
	@cd $^; zip -r ../../../$@ * $(ZIPIGNORES);cd -;


$(ZIPFILE): $(ZIPS)
	@echo "-------------------------------------------------------"
	@echo "Creating extension zip file: $(ZIPFILE)"
	@mv $(INSTALLS:=.zip) packages/
	@(cd $(PACKAGES); zip -r ../$@ * $(ZIPIGNORES))
	@echo "-------------------------------------------------------"
	@echo "Finished creating package $(ZIPFILE)."

