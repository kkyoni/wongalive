$('.data_table').DataTable( {
    "paging":   true,
    "ordering": true,
    "searching": false,
    "bLengthChange" : false,
    "order": [[ 0, 'desc' ], [ 1, 'desc' ],[ 2, 'desc' ],[ 3, 'desc' ],[ 4, 'desc' ],[ 5, 'desc' ],[ 6, 'desc' ]]
});


$('.data_table1').DataTable({ordering:false});

$('.historyDeposit').DataTable({ 
    "paging":true,
    "ordering": true,
    "searching": false,
    "bLengthChange" : false,
    "info": false, 
    "order": [[ 5, 'desc' ]]
});

$('.historyWithdrawal').DataTable({ 
    "paging":   true,
    "ordering": true,
    "searching": false,
    "bLengthChange" : false,
    "info": false,
    "order": [[ 4, 'desc' ]]
});

