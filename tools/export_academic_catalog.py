"""Exporta la fuente XLSX a JSON determinista; solo biblioteca estándar, sin Excel en runtime."""
from pathlib import Path
import hashlib
import json
import xml.etree.ElementTree as ET
import zipfile

ROOT = Path(__file__).resolve().parent.parent
SOURCE = ROOT / 'docs/reference/Catalogo_Academico_UNICAH_SAP_CORREGIDO.xlsx'
DESTINATION = ROOT / 'database/data/academic_catalog.json'
NS = {'s': 'http://schemas.openxmlformats.org/spreadsheetml/2006/main'}


def export():
    with zipfile.ZipFile(SOURCE) as archive:
        strings = []
        if 'xl/sharedStrings.xml' in archive.namelist():
            strings = [''.join(t.text or '' for t in item.findall('.//s:t', NS))
                       for item in ET.fromstring(archive.read('xl/sharedStrings.xml')).findall('s:si', NS)]
        relations = {r.attrib['Id']: r.attrib['Target'] for r in ET.fromstring(archive.read('xl/_rels/workbook.xml.rels'))}
        sheets = {}
        for sheet in ET.fromstring(archive.read('xl/workbook.xml')).findall('s:sheets/s:sheet', NS):
            target = relations[sheet.attrib['{http://schemas.openxmlformats.org/officeDocument/2006/relationships}id']]
            target = target.lstrip('/') if target.startswith('/') else 'xl/' + target
            rows = []
            for row in ET.fromstring(archive.read(target)).findall('s:sheetData/s:row', NS):
                values = {}
                for cell in row:
                    value = cell.find('s:v', NS)
                    text = value.text or '' if value is not None else ''.join(t.text or '' for t in cell.findall('.//s:t', NS))
                    values[''.join(c for c in cell.attrib['r'] if c.isalpha())] = strings[int(text)] if cell.attrib.get('t') == 's' and text else text
                if any(values.values()):
                    rows.append(values)
            sheets[sheet.attrib['name']] = rows
    payload = {'source': SOURCE.relative_to(ROOT).as_posix(), 'sha256': hashlib.sha256(SOURCE.read_bytes()).hexdigest(), 'sheets': sheets}
    DESTINATION.parent.mkdir(parents=True, exist_ok=True)
    DESTINATION.write_text(json.dumps(payload, ensure_ascii=False, indent=2) + '\n', encoding='utf-8')
    print('Exportadas %d filas de planes; salida: %s' % (len(sheets['PLANES_CARRERA']) - 1, DESTINATION.relative_to(ROOT)))


if __name__ == '__main__':
    export()
