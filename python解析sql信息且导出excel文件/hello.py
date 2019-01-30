#!/usr/bin/python
# -*- coding: gbk -*-

import os
import re
import datetime
import csv
import codecs

output_path="/Users/tanjian/web/python_projects/output.txt"
output_obj = open(output_path, 'a')
output_obj.write("[TOC]")
output_obj.write("\n")

# init
excel_column_list = []
excel_data_list = []


def _getData(table_name_list, column_comment_list):
    excel_data_list = []
    tb_name = table_name_list[0]
    for item in column_comment_list :
        item = item.strip()
        cell = []
        cell.append(tb_name)
        cell.append(item)

        excel_data_list.append(cell)

    return excel_data_list

# print 222
# quit()
try:
    for parent,dirs,files in os.walk("/Users/tanjian/web/python_projects/sqls") :

        for file in files :
            file = os.path.join(parent, file)
            file_object = open(file)

            try:
                all_the_text = file_object.read()

                #表名
                table_name_list=re.findall(r"CREATE TABLE(\s\S*)", all_the_text)

                #字段注释
                column_comment_list = re.findall(r"IS(\s\S+)\';",all_the_text)

            finally:
                file_object.close()

    # export
    t = datetime.datetime.now().strftime("%Y%m%d%h%i%s")
    export_file = 'excels/'+t + 'csv.csv'

    # 解决导出Excel乱码
    excel_file_obj = open(export_file, 'wb')
    excel_file_obj.write(codecs.BOM_UTF8)
    writer = csv.writer(excel_file_obj)

    # getData
    excel_column_list = ['tb_name', 'field_comment']

    excel_data_list = _getData(table_name_list, column_comment_list)

    writer.writerow(excel_column_list)
    writer.writerows(excel_data_list)
    excel_file_obj.close()

finally:
    output_obj.close()





