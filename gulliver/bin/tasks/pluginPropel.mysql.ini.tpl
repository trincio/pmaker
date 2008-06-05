propel.targetPackage       = model
propel.packageObjectModel  = true
propel.project             = {className}
propel.database            = mysql
propel.database.createUrl  = mysql://root@localhost/
propel.database.url        = mysql://root@localhost/{className}

propel.addGenericAccessors = true
propel.addGenericMutators  = true
propel.addTimeStamp        = false

propel.schema.validate     = false

; directories
propel.home                    = .
propel.output.dir              = .
propel.schema.dir              = ${propel.output.dir}config
propel.conf.dir                = ${propel.output.dir}config
propel.phpconf.dir             = ${propel.output.dir}config
propel.sql.dir                 = ${propel.output.dir}data/mysql
propel.runtime.conf.file       = runtime-conf.xml
propel.php.dir                 = ${propel.output.dir}
propel.default.schema.basename = schema
propel.datadump.mapper.from    = *schema.xml
propel.datadump.mapper.to      = *data.xml

; builder settings
;_propel.builder.peer.class              = addon.propel.builder.SfPeerBuilder
;propel.builder.object.class            = addon.propel.builder.SfObjectBuilder

;propel.builder.objectstub.class        = addon.propel.builder.SfExtensionObjectBuilder
;propel.builder.peerstub.class          = addon.propel.builder.SfExtensionPeerBuilder
;propel.builder.objectmultiextend.class = addon.propel.builder.SfMultiExtendObjectBuilder
;propel.builder.mapbuilder.class        = addon.propel.builder.SfMapBuilderBuilder
propel.builder.interface.class         = propel.engine.builder.om.php5.PHP5InterfaceBuilder
propel.builder.node.class              = propel.engine.builder.om.php5.PHP5NodeBuilder
propel.builder.nodepeer.class          = propel.engine.builder.om.php5.PHP5NodePeerBuilder
propel.builder.nodestub.class          = propel.engine.builder.om.php5.PHP5ExtensionNodeBuilder
propel.builder.nodepeerstub.class      = propel.engine.builder.om.php5.PHP5ExtensionNodePeerBuilder

propel.builder.addIncludes = false
propel.builder.addComments = false

propel.builder.addBehaviors = false
