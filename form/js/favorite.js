$(document).ready(function(){
    $('.favorite-btn').click(function(event){
        event.preventDefault(); // デフォルトのボタン動作を防止
        var id = $(this).data('id');
        var isFavorite = $(this).data('favorite');
        $.ajax({
            url: 'favorite.php',
            type: 'POST',
            data: { id: id, favorite: isFavorite },
            success: function(response) {
                if (response == 'success') {
                    var newFavorite = isFavorite == 1 ? 0 : 1;
                    $('button[data-id="' + id + '"]').data('favorite', newFavorite);
                    $('button[data-id="' + id + '"]').text(newFavorite == 1 ? '★' : '☆');
                }
            }
        });
    });
});
