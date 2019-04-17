from wb import *
import grt
from mforms import Utilities, FileChooser
import mforms

ModuleInfo = DefineModule(name="DBReport", author="Tito Sanchez", version="1.0", description="Database schema in HTML report format")

@ModuleInfo.plugin("rsn86.DBDocPy.htmlReportSchema", caption="Database schema report in HTML format", description="Database schema report in HTML format", input=[wbinputs.currentCatalog()], pluginMenu="Catalog")
@ModuleInfo.export(grt.INT, grt.classes.db_Catalog)

def htmlDataDictionary(catalog):
    htmlOut = ""
    filechooser = FileChooser(mforms.SaveFile)
    filechooser.set_extensions("HTML File (*.html)|*.html","html");
    if filechooser.run_modal():
       htmlOut = filechooser.get_path()
    print "HTML File: %s" % (htmlOut)
    if len(htmlOut) <= 1:
       return 1
    # iterate through columns from schema
    schema = catalog.schemata[0]
    htmlFile = open(htmlOut, "w")
    print >>htmlFile, "<html><head>"
    print >>htmlFile, "<title>Schema Report for database: %s</title>" % (schema.name)
    print >>htmlFile, """<style>
        td,th {
        text-align:left;
        vertical-align:middle;
        border: 1px solid;
        }
        table {
        border: none;
        border-collapse: collapse;
        }
        td {
        display: block;
        float: left;
        padding-left: 5px;
        padding-right: 5px;
        }
        </style>
      </head>
     <body>"""
    print >>htmlFile, "<h1>Schema Report for database: %s</h1>" % (schema.name)
    for table in schema.tables:
      print >>htmlFile, table.name
      print >>htmlFile, "<table>"
      print >>htmlFile, "<tr>"
      for column in table.columns:
        pk = bool(table.isPrimaryKeyColumn(column))
        fk = bool(table.isForeignKeyColumn(column))
        column_name = column.name
        column_name = "<u>%s</u>" % column.name if pk else column_name
        column_name = "<i>%s</i>" % column.name if fk else column_name
        print >>htmlFile, "<td>%s</td>" % column_name
      print >> htmlFile, "</tr>"
      print >> htmlFile, "</table><br>"
    print >>htmlFile, "</body></html>"
    Utilities.show_message("Report generated", "HTML Report format from current model generated", "OK","","")
    return 0
