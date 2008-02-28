<?xml version="1.0" encoding="UTF-8"?>
<dynaForm name="{className}" width="100%" type="pagedtable"
  sql="SELECT * from {tableName} "
  sqlConnection=""
>

<!-- START BLOCK : fields -->
<{name} type="text" colWidth='{size}' maxlength='{maxlength}' >
  <en>{name}</en>
</{name}>

<!-- END BLOCK : fields --> 

<LINK type="link" colWidth="60" titleAlign="left" align="left" link='{className}Edit?id={primaryKey}' >
  <en>Edit</en>
</LINK >

<LINK2 type="link" colWidth="60" titleAlign="left" align="left" link='{className}Delete?id={primaryKey}' >
  <en>Delete</en>
</LINK2 >

</dynaForm>