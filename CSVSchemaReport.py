from wb import *
import grt
from mforms import Utilities, FileChooser
import mforms

ModuleInfo = DefineModule(name="CSVReport", author="Thatchapon Unprasert", version="1.0", description="Database schema in CSV report format")

@ModuleInfo.plugin("self.python.csvReportSchema", caption="Database schema report in CSV format", description="Database schema report in CSV format", input=[wbinputs.currentCatalog()], pluginMenu="Catalog")
@ModuleInfo.export(grt.INT, grt.classes.db_Catalog)

def csvDataDictionary(catalog):
    csvOut = ""
    filechooser = FileChooser(mforms.OpenDirectory)
    filechooser.set_extensions("CSV File (*.csv)", "csv");
    if filechooser.run_modal():
       csvOut = filechooser.get_path()
    print "CSV File: %s" % (csvOut)
    if len(csvOut) <= 1:
       return 1
    # iterate through columns from schema
    schema = catalog.schemata[0]
    for table in schema.tables:
      csvFile = open("%s/%s.csv" % (csvOut, table.name), "w")
      print >>csvFile, "Name,Data Type,Nullable,PK,FK,Reference,Default,Comment"
      fks = table.foreignKeys
      for column in table.columns:
        pk = ('No', 'Yes')[bool(table.isPrimaryKeyColumn(column))]
        is_fk = bool(table.isForeignKeyColumn(column))
        fk = ('No', 'Yes')[is_fk]
        ref = find_referenced_table(fks, column) if is_fk else ''
        nn = ('No', 'Yes')[bool(column.isNotNull)]
        print >>csvFile, "%s,\"%s\",%s,%s,%s,%s,\"%s\",%s" % (column.name, column.formattedType, nn, pk, fk, ref, column.defaultValue, column.comment)
    Utilities.show_message("CSVs generated", "CSV data dictionaries from current model generated", "OK","","")
    return 0

def find_referenced_table(fks, column):
    for fk in fks:
        if fk.name.endswith("%s_foreign" % column.name):
            return fk.referencedTable.name
    return None