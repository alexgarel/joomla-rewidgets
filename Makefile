VERSION = $(shell grep "<version>" plugin/*.xml|cut  -d ">" -f 2|cut -d "<" -f 1)

zip:
	@echo "Creating zip file for version $(VERSION)"
	@(cd plugin && zip -r ../plg-rewidgets-$(VERSION).zip *)

all: zip
