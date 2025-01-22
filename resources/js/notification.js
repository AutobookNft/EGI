if (import.meta.env.MODE === 'development') {
    console.log('dentro notification.js');
}

if (typeof Livewire !== 'undefined') {

Livewire.on('success', (text) => {
    console.log(text);
    Swal.fire({
        title: text[0]['message'],
        html: text[0]['element'],
        width: 600,
        padding: "3em",
        color: "#00FF00",
        background: "#fff url(/images/trees.png)",
        });
});

Livewire.on('forbiddenTermFound', (text) => {
    console.log(text);
    Swal.fire({
        title: text[0]['title'],
        html: text[0]['message'],
        width: 600,
        padding: "3em",
        color: "#716add",
        background: "#fff url(/images/trees.png)",
        footer: text[0]['link'],
        backdrop: `
            rgba(0,0,123,0.4)
            url("/images/nyan-cat.gif")
            left top
            no-repeat
        `
        });
});

Livewire.on('confirm-invitation', (text) => {
    console.log(text);
    const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
        confirmButton: "btn btn-success",
        cancelButton: "btn btn-danger"
        },
        buttonsStyling: false
    });
    swalWithBootstrapButtons.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Yes, delete it!",
        cancelButtonText: "No, cancel!",
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
        swalWithBootstrapButtons.fire({
            title: "Deleted!",
            text: "Your file has been deleted.",
            icon: "success"
        });
        } else if (
        /* Read more about handling dismissals below */
        result.dismiss === Swal.DismissReason.cancel
        ) {
        swalWithBootstrapButtons.fire({
            title: "Cancelled",
            text: "Your imaginary file is safe :)",
            icon: "error"
        });
        }
    });
});

Livewire.on('generic_error', (text) => {
    console.log(text);
    Swal.fire({
        icon: "error",
        title: "Oops...",
        text: text[0]['message'],
    });
});

Livewire.on('sureMergeTraitToEGI', (text) => {
    console.log(text);
    Swal.fire({
        title: text[0]['title'],
        text: text[0]['message'],
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: text[0]['confirmButtonText'],
        cancelButtonText: text[0]['cancelButtonText']
    }).then((result) => {
        if (result.isConfirmed) {
            // Invia un evento Livewire per chiamare il metodo mergeTraitToEGI
            Livewire.dispatch('mergeTraitToEGI');

            // Mostra un messaggio di successo
            Swal.fire({
                title: text[0]['result_title'],
                text: text[0]['result_message'],
                icon: 'success',
            });
        }
    });
});
}else{
    console.log('Livewire non definito');
}

