addEvent(window, "load", sortables_init);

var SORT_COLUMN_INDEX;

function sortables_init() {
  // Find all tables with class sortable and make them sortable
  if (!document.getElementsByTagName) return;
  var tbls = document.getElementsByTagName("table");
  for (var ti = 0; ti < tbls.length; ti++) {
    var thisTbl = tbls[ti];
    if ((" " + thisTbl.className + " ").indexOf("sortable") != -1) {
      ts_makeSortable(thisTbl);
    }
  }
}

function ts_makeSortable(table) {
  if (table.rows && table.rows.length > 0) {
    var firstRow = table.rows[0];
  }
  if (!firstRow) return;

  // We have a first row: assume it's the header, and make its contents clickable links
  for (var i = 0; i < firstRow.cells.length; i++) {
    var cell = firstRow.cells[i];
    var txt = ts_getInnerText(cell);
    if (
      cell.className != "unsortable" &&
      cell.className.indexOf("unsortable") == -1
    ) {
      cell.innerHTML =
        '<a href="#" class="sortheader" onclick="tsresortTable(this);return false;">' +
        txt +
        '<span class="sortarrow">&nbsp;&nbsp;</span></a>';
    }
  }
}

function ts_getInnerText(el) {
  if (typeof el == "string") return el;
  if (typeof el == "undefined") return el;
  if (el.innerText) return el.innerText; // Not needed but it is faster
  var str = "";

  var cs = el.childNodes;
  var l = cs.length;
  for (var i = 0; i < l; i++) {
    switch (cs[i].nodeType) {
      case 1: //ELEMENT_NODE
        str += ts_getInnerText(cs[i]);
        break;
      case 3: //TEXT_NODE
        str += cs[i].nodeValue;
        break;
    }
  }
  return str;
}

function tsresortTable(lnk) {
  console.log(lnk);
  var span;
  for (var ci = 0; ci < lnk.childNodes.length; ci++) {
    if (
      lnk.childNodes[ci].tagName &&
      lnk.childNodes[ci].tagName.toLowerCase() == "span"
    )
      span = lnk.childNodes[ci];
  }
  var td = lnk.parentNode;
  var column = td.cellIndex;
  var table = getParent(td, "TABLE");

  // Work out a type for the column
  if (table.rows.length <= 1) return;
  var itm = ts_getInnerText(table.rows[1].cells[column]);
  sortfn = ts_sort_caseinsensitive;
  if (itm.match(/^\d\d[\/\.-][a-zA-Z][a-zA-Z][a-zA-Z][\/\.-]\d\d\d\d$/))
    sortfn = ts_sort_date;
  if (itm.match(/^\d\d[\/\.-]\d\d[\/\.-]\d\d\d\d$/)) sortfn = ts_sort_date;
  if (itm.match(/^[\d\.]+$/)) sortfn = ts_sort_numeric;

  SORT_COLUMN_INDEX = column;
  var newRows = new Array();
  for (var j = 1; j < table.rows.length; j++) {
    newRows[j - 1] = table.rows[j];
  }

  newRows.sort(sortfn);

  if (span.getAttribute("sortdir") == "down") {
    ARROW = "&nbsp;&nbsp;&#x25B2;";
    newRows.reverse();
    span.setAttribute("sortdir", "up");
  } else {
    ARROW = "&nbsp;&nbsp;&#x25BC;";
    span.setAttribute("sortdir", "down");
  }

  // We appendChild rows that already exist to the tbody, so it moves them rather than creating new ones
  for (var i = 0; i < newRows.length; i++) {
    table.tBodies[0].appendChild(newRows[i]);
  }

  // Delete any other arrows there may be showing
  var allspans = document.getElementsByTagName("span");
  for (var ci = 0; ci < allspans.length; ci++) {
    if (allspans[ci].className == "sortarrow") {
      if (getParent(allspans[ci], "table") == getParent(lnk, "table")) {
        allspans[ci].innerHTML = "&nbsp;&nbsp;";
      }
    }
  }

  span.innerHTML = ARROW;
}
window.tsresortTable = tsresortTable;
function getParent(el, pTagName) {
  if (el == null) return null;
  else if (
    el.nodeType == 1 &&
    el.tagName.toLowerCase() == pTagName.toLowerCase()
  )
    return el;
  else return getParent(el.parentNode, pTagName);
}

function ts_sort_date(a, b) {
  var aa = ts_getInnerText(a.cells[SORT_COLUMN_INDEX]);
  var bb = ts_getInnerText(b.cells[SORT_COLUMN_INDEX]);
  var dt1 = aa.substr(6, 4) + aa.substr(3, 2) + aa.substr(0, 2);
  var dt2 = bb.substr(6, 4) + bb.substr(3, 2) + bb.substr(0, 2);
  if (dt1 == dt2) return 0;
  if (dt1 < dt2) return -1;
  return 1;
}

function ts_sort_numeric(a, b) {
  var aa = parseFloat(ts_getInnerText(a.cells[SORT_COLUMN_INDEX]));
  if (isNaN(aa)) aa = 0;
  var bb = parseFloat(ts_getInnerText(b.cells[SORT_COLUMN_INDEX]));
  if (isNaN(bb)) bb = 0;
  return aa - bb;
}

function ts_sort_caseinsensitive(a, b) {
  var aa = ts_getInnerText(a.cells[SORT_COLUMN_INDEX]).toLowerCase();
  var bb = ts_getInnerText(b.cells[SORT_COLUMN_INDEX]).toLowerCase();
  if (aa == bb) return 0;
  if (aa < bb) return -1;
  return 1;
}

function addEvent(elm, evType, fn, useCapture) {
  if (elm.addEventListener) {
    elm.addEventListener(evType, fn, useCapture || false);
    return true;
  } else if (elm.attachEvent) {
    return elm.attachEvent("on" + evType, fn);
  }
  return false;
}
