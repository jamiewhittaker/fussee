var elements = document.getElementsByClassName("favourite");

var myFunction = function() {

    var attribute = this.getAttribute("data-id");

    $.ajax({
        type: 'post',
        url: 'removefavourite',
        data: {
            'recipeID': attribute,
        },
        success: function () {
            console.log("Removed from DB")
        },
        error: function (XMLHttpRequest) {
            console.log("Failed to remove from DB")
        }
    });

    this.innerHTML = "<i class=\"far fa-heart\"></i>";
    this.className = "favourite-clicked";
    this.parentElement.style.display = "none";

};

for (var i = 0; i < elements.length; i++) {
    elements[i].addEventListener('click', myFunction, false);
}