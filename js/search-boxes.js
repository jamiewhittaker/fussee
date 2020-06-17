document.addEventListener('DOMContentLoaded', () => {
    var firstElement = document.getElementById('yesIngredients');
    var secondElement = document.getElementById('noIngredients');

    var choices1 = new Choices(firstElement, {
        delimiter: ',',
        editItems: true,
        maxItemCount: 5,
        removeItemButton: true,
        duplicateItemsAllowed: false,
        allowPaste: false
    })

    var choices2 = new Choices(secondElement, {
        delimiter: ',',
        editItems: true,
        maxItemCount: 5,
        removeItemButton: true,
        duplicateItemsAllowed: false,
        allowPaste: false
    })


});