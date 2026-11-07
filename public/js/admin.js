function deleteAnonReport(id) {
    if (confirm('Apakah anda yakin ingin menghapus laporan anonim ini?')) {
        $.ajax({
            url: `/admin/reports/delete-anon/${id}`,
            method: 'POST',
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    location.reload();
                } else {
                    alert(response.message);
                }
            },
            error: function(xhr) {
                alert('Error: ' + xhr.responseJSON.message);
            }
        });
    }
}