// Call the dataTables jQuery plugin
$(document).ready(function() {
  $('#dataTable').DataTable({
    "order": [[ 0, "desc" ]],
    "lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
    "pageLength": 5
  } );
} );

// Call the dataTables jQuery plugin
$(document).ready(function() {
  $('#dataTable1').DataTable({
    "order": [[ 0, "desc" ]],
    "lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
    "pageLength": 5
  } );
} );

// Call the dataTables jQuery plugin
$(document).ready(function() {
    $('#pencapaianModal').DataTable({
      "order": [[ 0, "desc" ]],
      "lengthMenu": [[3, 5, 10 -1], [3, 5, 10, "All"]],
      "pageLength": 3
    } );
} );

// Call the dataTables jQuery plugin
$(document).ready(function() {
  $('#dataPr').DataTable({
    "order": [[ 0, "asc" ]],
    "lengthMenu": [[5, 7, 10, 25, 50, -1], [5, 7, 10, 25, 50, "All"]],
    "pageLength": 7
  } );
} );

// Call the dataTables jQuery plugin
$(document).ready(function() {
  $('#dataPo').DataTable({
    "order": [[ 0, "asc" ]],
    "lengthMenu": [[5, 7, 10, 25, 50, -1], [5, 7, 10, 25, 50, "All"]],
    "pageLength": 7
  } );
} );

// Call the dataTables jQuery plugin
$(document).ready(function() {
    $('#listPencapaianUser').DataTable({
      "order": [[ 0, "asc" ]],
      "lengthMenu": [[5, 7, 10, 25, 50, -1], [5, 7, 10, 25, 50, "All"]],
      "pageLength": 7
    } );
  } );

$(document).ready(function() {
  $('#docLetter').DataTable({
    "order": [[ 0, "asc" ]],
    "lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
    "pageLength": 25
  } );
} );

$(document).ready(function() {
    $('#listRoles').DataTable({
        "order": [[ 0, "asc" ]],
        "lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
        "pageLength": 25
    } );
} );

$(document).ready(function() {
    $('#listPage').DataTable({
        "order": [[ 0, "asc" ]],
        "lengthMenu": [[5, 7, 10, 25, 50, -1], [5, 7, 10, 25, 50, "All"]],
        "pageLength": 7
    } );
} );


$(document).ready(function() {
  $('#docLetterGuest').DataTable({
    "order": [[ 3, "desc" ]],
    "lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
    "pageLength": 25
  } );
} );

$(document).ready(function() {
    $('#receivableNotes').DataTable({
      "order": [[ 1, "desc" ]],
      "lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
      "pageLength": 25
    } );
  } );

$(document).ready(function() {
    $('#receivableNoteItems').DataTable({
        "order": [[ 1, "asc" ]],
        "lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
        "pageLength": 25
    } );
} );

$(document).ready(function() {
    $('#receivableNoteItemsUsers').DataTable({
        "order": [[ 0, "asc" ]],
        "lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
        "pageLength": 25
    } );
} );


$(document).ready(function() {
    $('#requestForm').DataTable({
        "order": [[ 0, "desc" ]],
        "lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
        "pageLength": 5
    } );
} );
