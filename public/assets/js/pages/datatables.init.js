$(document).ready(function () {
    $("#datatable").DataTable();
    $("#datatable-buttons").DataTable({
        lengthChange: false,
        buttons: ["copy", "excel", "pdf", "colvis"],
    });
    $(".dataTables_length select").addClass("form-select form-select-sm");
});
