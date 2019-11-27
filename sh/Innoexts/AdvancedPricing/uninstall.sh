#!/bin/sh
CWD=$(pwd)

rm -fr $CWD/app/code/community/Innoexts/Core/
rm -fr $CWD/app/design/adminhtml/default/default/template/innoexts/core/
rm -f $CWD/app/etc/modules/Innoexts_Core.xml
rm -f $CWD/app/locale/en_US/Innoexts_Core.csv
rm -fr $CWD/js/innoexts/core/
rm -fr $CWD/shell/Innoexts/Core/
rm -fr $CWD/app/code/community/Innoexts/GeoCoder/
rm -f $CWD/app/etc/modules/Innoexts_GeoCoder.xml
rm -f $CWD/app/locale/en_US/Innoexts_GeoCoder.csv
rm -fr $CWD/app/code/community/Innoexts/GeoIp/
rm -f $CWD/app/etc/modules/Innoexts_GeoIp.xml
rm -f $CWD/app/locale/en_US/Innoexts_GeoIp.csv
rm -fr $CWD/app/code/community/Innoexts/CustomerLocator/
rm -f $CWD/app/design/frontend/base/default/layout/innoexts/customerlocator.xml
rm -fr $CWD/app/design/frontend/base/default/layout/innoexts/customerlocator/
rm -f $CWD/app/etc/modules/Innoexts_CustomerLocator.xml
rm -f $CWD/app/locale/en_US/Innoexts_CustomerLocator.csv
rm -fr $CWD/skin/frontend/base/default/innoexts/customerlocator/
rm -fr $CWD/app/code/community/Innoexts/AdvancedPricing/
rm -f $CWD/app/design/adminhtml/default/default/layout/innoexts/advancedpricing.xml
rm -fr $CWD/app/design/adminhtml/default/default/layout/innoexts/advancedpricing/
rm -f $CWD/app/etc/modules/Innoexts_AdvancedPricing.xml
rm -f $CWD/app/locale/en_US/Innoexts_AdvancedPricing.csv
rm -fr $CWD/js/innoexts/advancedpricing/
rm -fr $CWD/shell/Innoexts/AdvancedPricing/
rm -fr $CWD/skin/adminhtml/default/default/innoexts/advancedpricing/
rm -fr $CWD/sql/Innoexts/AdvancedPricing/
rm -f $CWD/package/Innoexts_AdvancedPricing.xml
rm -fr $CWD/var/import/Innoexts/AdvancedPricing/
rm -fr $CWD/var/export/Innoexts/AdvancedPricing/
