-- default values for table: paragraph_property

UPDATE "_central"."paragraph_property"
   SET "value"  = REPLACE( "value", 'All rights reserved! 2012 GridZ', 'Powered by GridGuyz' )
 WHERE "locale" <> '*'
   AND "name"   = 'html';

UPDATE "_central"."paragraph_property"
   SET "value"  = '1'
 WHERE "locale" = '*'
   AND "name"   = 'menuId'
   AND "value"  <> '1';
