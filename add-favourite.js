var elements = document.getElementsByClassName("favourite");

var myFunction = function() {

    var attribute = this.getAttribute("data-id");

    $.ajax({
        type: 'post',
        url: 'addfavourite',
        data: {
            'recipeID': attribute,
        },
        success: function () {
            console.log("Added to DB")
        },
        error: function (XMLHttpRequest) {
            console.log("Failed to add to DB")
        }
    });

    this.innerHTML = "<i class=\"fas fa-heart\"></i>";
    this.className = "favourite-clicked";

};

for (var i = 0; i < elements.length; i++) {
    elements[i].addEventListener('click', myFunction, false);
}