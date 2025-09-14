$(function () {
    //alert(1);
    //console.log( "ready!" );
    // $('.card')
    //   .mouseover(function(){
    //       $(this)
    //       .find('.view-card')
    //       .show()
    //       .width($('.view-card').parent().width())
    //       .height($('.view-card').parent().height())
    //       //.css({ backgroundColor: "grey", opacity: "0.1" });
    //   })
    //   .mouseout(function(){
    //     $(this)
    //       .find('.view-card')
    //       .hide(); 
    //   });
    // $('.card')
    //     .mouseover(function () {
    //         //conso(1);
    //         $(this)
    //         .find('.card-footer')
    //         //.slideDown("slow")
    //         .show('slow');
    //     })
    //     .mouseout(function () {
    //         $(this)
    //         .find('.card-footer')
    //         //.slideDown("slow")
    //         .hide('slow')
    //     });

    //For the bootstrap input file to work we need to assing the input 
    //value to the label
    $('#inputGroupFile03').on('change', function () {
        let fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').addClass("selected").html(fileName);
    });

    //Change the Views from Grid to List
    $('.views i').click(function () {
        //let grid = $('.cards').clone();
        //Then is grid view
        if ($(this).hasClass('fa-th-large')) {
            $('.cards-container').show('slow');
            if ($('table.view-list').length) {
                $('table.view-list').hide('slow');
            }
        }
            //Else is list
            else {
                $('.cards-container').hide('slow');
                if ($('table.view-list').length) {
                    $('table.view-list').show('slow');
                }
                else {
                    let cardArray = $('.card');
                    let titles = [];
                    let qty = [];
                    let description = [];
                    let btnEdit = [];
                    let btnView = [];
                    let itemIds = [];
                    let updatedAt = [];
                    $.each(cardArray, function (index, value) {                      
                        titles.push($(value).find('.card-title').html());
                        qty.push($(value).find('.qty>span').html());
                        description.push($(value).find('.description').html());
                        btnEdit.push($(value).find('.card-footer a.btn-edit').get());
                        btnView.push($(value).find('.card-footer a.btn-view').get());
                        // Extract ID from edit button URL
                        let editUrl = $(value).find('.card-footer a.btn-edit').attr('href');
                        let id = editUrl ? editUrl.split('id=')[1] : '';
                        itemIds.push(id);
                        // Extract updated_at from the last updated text
                        let updatedText = $(value).find('.card-text small').text();
                        let updatedMatch = updatedText.match(/Last updated: (.+)/);
                        updatedAt.push(updatedMatch ? updatedMatch[1] : 'Unknown');
                    });
                    let tablePartial = '';
                    for (let i = 0; i < cardArray.length; i++) {
                        // btnView[i] = $(btnView[i]);
                        // btnEdit[i] = $(btnEdit[i]);
                        tablePartial += `<tr>
                                    <td>
                                    ${i + 1}
                                    </td>
                                    <td>
                                    ${titles[i]}
                                    </td>
                                    <td>
                                    ${qty[i]}
                                    </td>
                                    <td>
                                    ${description[i]}
                                    </td>
                                    <td>
                                    <small class="text-muted">
                                        <i class="fas fa-clock" aria-hidden="true"></i>
                                        ${updatedAt[i]}
                                    </small>
                                    </td>
                                    <td>
                                    <a href="/index.php?script=viewItem&id=${itemIds[i]}" class="btn btn-primary btn-view">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="${btnEdit[i]}" class="btn btn-success btn-edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    </td>
                                </tr>`;
                    }
                    $('.cards').append(`
            <div class="table-responsive-sm">
            <table class="table table-hover view-list" style="background-color: white;">
            <thead>
              <tr>
                <th scope="col">#</th>
                <th scope="col">Title</th>
                <th scope="col">Quantity</th>
                <th scope="col">Description</th>
                <th scope="col">Last Updated</th>
                <th scope="col">Actions</th>
              </tr>
            </thead>
            <tbody>
            ${tablePartial}
            </tbody>
            </table>
            </div>
            `);
                    //console.log(btnEdit);
                    // console.log(qty);
                    // console.log(description);

                    // console.log($('.cards').find('.card'));
                }


            }

    });

});