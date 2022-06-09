// fenetre modale de confirmation de suppression d'une conference de la page index.twig.html
$(document).ready(function() {
    $('#confirmDelete').on('show.bs.modal', function(e) {
        $(this).find('.oui').attr('href', $(e.relatedTarget).data('href'));
    });
}