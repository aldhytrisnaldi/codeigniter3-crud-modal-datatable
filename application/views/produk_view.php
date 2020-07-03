<!DOCTYPE html>
<html>
    <head> 
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CRUD CODEIGNITER</title>
    <link href="<?php echo base_url('assets/bootstrap/css/bootstrap.min.css')?>" rel="stylesheet">
    <link href="<?php echo base_url('assets/datatables/css/dataTables.bootstrap.min.css')?>" rel="stylesheet">
    <link href="<?php echo base_url('assets/bootstrap-datepicker/css/bootstrap-datepicker3.min.css')?>" rel="stylesheet">
    </head> 
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-fixed-top navbar-inverse">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="<?=site_url()?>">HOME</a>
            </div>
            <div id="navbar" class="navbar-collapse collapse">
                <ul class="nav navbar-nav">
                    <li class=""><a href="<?=site_url()?>">PRODUK</a></li>
                </ul>
                <ul class="nav navbar-nav navbar-right">
                    <li>
                        <a href="<?=site_url()?>">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    
    <!-- Konten & Tabel -->
    <div class="container">
        <h1 style="font-size:20pt">NEKODING</h1>

        <h3>DATA PRODUK</h3>
        <br />
        <button class="btn btn-sm btn-info" onclick="add_produk()"><i class="glyphicon glyphicon-plus"></i> Tambah Produk</button>
        <button class="btn btn-sm btn-default" onclick="reload_table()"><i class="glyphicon glyphicon-refresh"></i> Segarkan</button>
        <br />
        <br />
        <table id="table" class="table table-striped table-bordered" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>ID PRODUK</th>
                    <th>ID KATEGORI</th>
                    <th>NAMA PRODUK</th>
                    <th>KODE PRODUK</th>
                    <th>FOTO PRODUK</th>
                    <th>TGL REGISTER</th>
                    <th style="width:150px;">AKSI</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>

<script src="<?php echo base_url('assets/jquery/jquery-2.1.4.min.js')?>"></script>
<script src="<?php echo base_url('assets/bootstrap/js/bootstrap.min.js')?>"></script>
<script src="<?php echo base_url('assets/datatables/js/jquery.dataTables.min.js')?>"></script>
<script src="<?php echo base_url('assets/datatables/js/dataTables.bootstrap.min.js')?>"></script>
<script src="<?php echo base_url('assets/bootstrap-datepicker/js/bootstrap-datepicker.min.js')?>"></script>


<script type="text/javascript">
    var save_method;
    var table;
    var base_url = '<?php echo base_url();?>';

    $(document).ready(function() {
        table = $('#table').DataTable({ 
            "processing": true,
            "serverSide": true,
            "order": [],

            "ajax": {
                "url": "<?php echo site_url('produk/ajax_list')?>",
                "type": "POST"
            },

            "columnDefs": [
                { 
                    "targets": [ -1 ],
                    "orderable": false,
                },
                { 
                    "targets": [ -3 ],
                    "orderable": false,
                },
            ],

        });

        $('.datepicker').datepicker({
            autoclose: true,
            format: "yyyy-mm-dd",
            todayHighlight: true,
            orientation: "top auto",
            todayBtn: true,
            todayHighlight: true,  
        });

        $("input").change(function(){
            $(this).parent().parent().removeClass('has-error');
            $(this).next().empty();
        });
        $("textarea").change(function(){
            $(this).parent().parent().removeClass('has-error');
            $(this).next().empty();
        });
        $("select").change(function(){
            $(this).parent().parent().removeClass('has-error');
            $(this).next().empty();
        });
    });

    function add_produk()
    {
        save_method = 'add';
        $('#form')[0].reset();
        $('.form-group').removeClass('has-error');
        $('.help-block').empty();
        $('#modal_form').modal('show');
        $('.modal-title').text('TAMBAH PRODUK');
        $('#photo-preview').hide();
        $('#label-photo').text('Upload Photo');
    }

    function save()
    {
        $('#btnSave').text('Menyimpan...');
        $('#btnSave').attr('disabled',true);
        var url;

        if(save_method == 'add') {
            url = "<?php echo site_url('produk/ajax_add')?>";
        } else {
            url = "<?php echo site_url('produk/ajax_update')?>";
        }

        var formData = new FormData($('#form')[0]);
        $.ajax({
            url : url,
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            dataType: "JSON",
            success: function(data)
            {
                if(data.status)
                {
                    $('#modal_form').modal('hide');
                    reload_table();
                }
                else
                {
                    for (var i = 0; i < data.inputerror.length; i++) 
                    {
                        $('[name="'+data.inputerror[i]+'"]').parent().parent().addClass('has-error');
                        $('[name="'+data.inputerror[i]+'"]').next().text(data.error_string[i]);
                    }
                }
                $('#btnSave').text('SIMPAN');
                $('#btnSave').attr('disabled',false);
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Error adding / update data');
                $('#btnSave').text('SIMPAN');
                $('#btnSave').attr('disabled',false);

            }
        });
    }

    function edit_produk(id)
    {
        save_method = 'update';
        $('#form')[0].reset();
        $('.form-group').removeClass('has-error');
        $('.help-block').empty();

        $.ajax({
            url : "<?php echo site_url('produk/ajax_edit')?>/" + id,
            type: "GET",
            dataType: "JSON",
            success: function(data)
            {

                $('[name="id_produk"]').val(data.id_produk);
                $('[name="nama_produk"]').val(data.nama_produk);
                $('[name="kode_produk"]').val(data.kode_produk);
                $('[name="id_kategori"]').val(data.id_kategori);
                $('[name="tgl_register"]').datepicker('update',data.tgl_register);
                $('#modal_form').modal('show');
                $('.modal-title').text('Edit produk');
                $('#photo-preview').show();

                if(data.foto_produk)
                {
                    $('#label-photo').text('Ganti Foto');
                    $('#photo-preview div').html('<img src="'+base_url+'upload/'+data.foto_produk+'" class="img-responsive">');
                    $('#photo-preview div').append('<input type="checkbox" name="remove_photo" value="'+data.foto_produk+'"/> Remove photo when saving'); // remove photo

                }
                else
                {
                    $('#label-photo').text('Unggah Foto');
                    $('#photo-preview div').text('(Tidak ada Foto)');
                }
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Error get data from ajax');
            }
        });
    }

    function reload_table()
    {
        table.ajax.reload(null,false);
    }

    function delete_produk(id)
    {
        if(confirm('Anda yakin ingin menghapus produk ini.?'))
        {
            $.ajax({
                url : "<?php echo site_url('produk/ajax_delete')?>/"+id,
                type: "POST",
                dataType: "JSON",
                success: function(data)
                {
                    $('#modal_form').modal('hide');
                    reload_table();
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    alert('Error deleting data');
                }
            });
        }
    }

</script>

<!-- Form Modal -->
<div class="modal fade" id="modal_form" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">TAMBAH PRODUK</h3>
            </div>
            <div class="modal-body form">
                <form action="#" id="form" class="form-horizontal">
                    <input type="hidden" value="" name="id_produk"/> 
                    <div class="form-body">
                        <div class="form-group">
                            <label class="control-label col-md-3">NAMA PRODUK</label>
                            <div class="col-md-9">
                                <input name="nama_produk" placeholder="Nama Produk" class="form-control" type="text">
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">KODE PRODUK</label>
                            <div class="col-md-9">
                                <input name="kode_produk" placeholder="Kode Produk" class="form-control" type="text">
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">ID KATEGORY</label>
                            <div class="col-md-9">
                                <input name="id_kategori" placeholder="Id Kategori" class="form-control" type="text">
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">TGL REGISTER</label>
                            <div class="col-md-9">
                                <input name="tgl_register" placeholder="yyyy-mm-dd" class="form-control datepicker" type="text">
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group" id="photo-preview">
                            <label class="control-label col-md-3">FOTO PRODUK</label>
                            <div class="col-md-9">
                                (TIDAK ADA FOTO)
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3" id="label-photo">UNGGAH FOTO </label>
                            <div class="col-md-9">
                                <input name="photo" type="file">
                                <span class="help-block"></span>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnSave" onclick="save()" class="btn btn-sm btn-primary">SIMPAN</button>
                <button type="button" class="btn btn-sm btn-danger" data-dismiss="modal">BATALKAN</button>
            </div>
        </div>
    </div>
</div>

</body>
</html>