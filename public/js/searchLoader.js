//If I do it using AJAX the cards style is lost
$(function () {
  $('#searchTerm').keydown(function (event) {
    //alert(1);
    if (event.key === "Enter") {
      event.preventDefault();
      $.post("/searchScript.php",
        { searchTerm: $(this).val()},
        buildCards)
      // .done(function() {
      //   alert( "second success" );
      // })
      // .fail(function(e) {
      //   console.log(e);

      //   alert( e);
      // })
      // .always(function() {
      //   alert( "finished" );
      // });;
      //alert(1);
      // Do more work
    }
  });
});
function buildCards(items) {
  if (items.length > 0) {
    let containerCards = ``;
    items.forEach(function (item) {
      let description = item.description || '';
      let title = item.title;
      let qty = item.qty;
      let id = item.id;
      let img = item.img || 'image-not-found.png';

      containerCards += `
            <div class="card h-100">
                        <img src="/uploads/${img}" class="card-img-top" alt="My Saw saw" style="height: 209px;" />
            <div class="card-body">
                <h5 class="card-title" style="font-family: 'Rancho', serif; font-size:2em;">
                  ${title}
                </h5>
                <p class="card-text qty"><small class="text-muted">Number of items: </small>
                  <span class="badge badge-primary">
                    ${qty}
                  </span>
                </p>
                <p class="card-text description">
                  ${description}
                </p>
            </div>
            <div class="card-footer">
                <div class="row">
                    <div class="col-sm-6 mb-2">
                        <a class="btn btn-primary btn-view btn-block" href="#">
                            <i class="fas fa-eye"></i>
                            View
                        </a>
                    </div>
                    <div class="col-sm-6">
                        <a href="/index.php?script=editItem&amp;id=${id}" class="btn btn-success btn-edit btn-block" style="opacity:1;">
                            <i class="fas fa-edit"></i>
                            Edit
                        </a>
                    </div>
                </div>
            </div>
        </div>
`;
    });
    $('.cards-container').append(containerCards);
  }
}