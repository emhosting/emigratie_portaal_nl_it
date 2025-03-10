(function($){
    $(document).ready(function(){
        // Admin specifieke JavaScript functionaliteiten
        $('#add-category-btn').on('click', function(e){
            e.preventDefault();
            var categoryName = prompt("Voer categorienaam in:");
            if(categoryName && categoryName.trim() !== ""){
                $.ajax({
                    url: ajax_object.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'add_category',
                        name: categoryName
                    },
                    success: function(response){
                        if(response.success){
                            location.reload();
                        } else {
                            console.error("Categorie toevoegen mislukt: " + (response.data && response.data.message ? response.data.message : "Onbekende fout"));
                        }
                    },
                    error: function(xhr, status, error){
                        console.error("AJAX fout bij categorie toevoegen:", error);
                    }
                });
            } else {
                alert("Geen geldige categorienaam ingevuld.");
            }
        });
    });
})(jQuery);