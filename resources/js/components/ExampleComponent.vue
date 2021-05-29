<template>
    <div class="container">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">Usuarios</h1>
        </div>        

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-head">
                        <div class="card-header">
                            <h2>Lista de usuarios</h2>
                            <a class="heading-elements-toggle">
                                <i class="la la-ellipsis-v font-medium-3"></i>
                            </a>

                            <div class="heading-elements" v-if="modal == 0">
                                <button class="btn btn-success btn-sm" data-toggle="modal" data-target="#usersModal" @click="openModal('user','register')" type="button">
                                    <i class="ft-plus white"></i>
                                    Nuevo Usuario
                                </button>                               
                            </div>
                        </div>
                    </div>

                    <!-- Buscador -->
                    
                    <div class="card-body">
                        <div class="bug-list-search">
                            <div class="bug-list-search-content">
                                <div class="position-relative">
                                    <input type="text" @keyup.enter="listUsers(1, search, criterion)" v-model="search" class="form-control" placeholder="Ingrese el nombre del usuario a buscar y presione enter.">
                                    <div class="form-control-position">
                                        <i class="la la-search text-size-base text-muted la-rotate-270"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabla -->

                    <div class="table-responsive">
                        <table class="table table-striped table-sm">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nombre</th>
                                    <th>Correo electrónico</th>
                                    <th>Fecha de registro</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="user in users" :key="user.id">
                                    <td>{{ user.id }}</td>
                                    <td>{{ user.name }}</td>
                                    <td>{{ user.email }}</td>
                                    <td>{{ date(user.created_at) }}</td>
                                    <td>
                                        <button class="btn btn-primary btn-sm" @click="openModal('user','update',user)" type="button" data-toggle="modal" data-target="#usersModal">
                                            <i class="fa fas-pencil"></i>
                                            Editar
                                        </button>

                                        <button class="btn btn-danger btn-sm" type="button" v-on:click.prevent="deleteUser(user.id)" title="Eliminar usuario">
                                            <i class="la la-trash-o"></i>  
                                            Eliminar
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginador -->

                    <div class="text-center mb-3">
                        <nav aria-label="Page navigation">
                            <ul class="pagination justify-content-center pagination-round">
                                <li class="page-item" v-if="pagination.current_page > 1">
                                    <a class="page-link" href="#" aria-label="Previous" @click.prevent="changePage(pagination.current_page - 1, search, criterion)">
                                        <span aria-hidden="true">&laquo; Prev</span>
                                        <span class="sr-only">Previous</span>
                                    </a>
                                </li>

                                <li class="page-item" v-for="page in pagesNumber" :key="page" :class="[page == isActived ? 'active' : '']">
                                    <a class="page-link" href="#" @click.prevent="changePage(page, search, criterion)" v-text="page"></a>
                                </li>

                                <li class="page-item" v-if="pagination.current_page < pagination.last_page">
                                    <a class="page-link" href="#" aria-label="Next" @click.prevent="changePage(pagination.current_page + 1, search, criterion)">
                                        <span aria-hidden="true">Next &raquo;</span>
                                        <span class="sr-only">Next</span>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>               
            </div>
        </div>

        <!-- Modal  -->

        <div  class="modal fade" :class="{'mostrar' : modal}" tabindex="-1" role="dialog" id="usersModal" aria-labelledby="exampleModalLabel1" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <section class="contact-form">
                        <form class="contact-input" enctype="multipart/form-data" action="#" method="POST">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel1" v-text="titleModal"></h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="closeModal()">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>

                            <div class="modal-body">
                                <fieldset class="form-group col-12">
                                    <input type="text" class="form-control" placeholder="Nombre" v-model="name">
                                </fieldset>

                                <fieldset class="form-group col-12">
                                    <input type="email" class="form-control" placeholder="Correo electronico" v-model="email">
                                </fieldset>                                         

                                <fieldset class="form-group col-12" v-if="typeOption==1">
                                    <input type="password" class="form-control" placeholder="Contraseña" v-model="password">
                                </fieldset>
                            </div>

                            <div class="modal-footer">
                                <fieldset class="form-group position-relative has-icon-left mb-0">
                                    <input type="button" class="btn btn-outline-danger btn-sm" @click="closeModal()" value="Cerrar modal">
                                    <input type="button" v-if="typeOption==1" class="btn btn-outline-success btn-sm" @click.prevent="addUser()" value="Guardar">
                                    <input type="button" v-if="typeOption==2" class="btn btn-outline-success btn-sm" @click.prevent="updateUser(id)" value="Actualizar">
                                </fieldset>
                            </div>
                        </form>
                    </section>
                </div>
            </div>
        </div>        
    </div>
</template>

<script>
    import axios from 'axios'
    import VueAxios from 'vue-axios'
    import toastr from 'toastr'
    import moment from 'moment'
    import Swal from 'sweetalert2'

    moment.locale('es');

    export default {
        data(){
            return {
                id: 0,
                users:[],
                criterion: 'name',
                search: '',
                pagination: {
                    'total': 0,
                    'current_page': 0,
                    'per_page': 0,
                    'last_page': 0,
                    'from': 0,
                    'to': 0
                },
                offset: 3,
                errorMensaje: [],
                validationErrors: [],
                titleModal: '',
                modal: 0,
                typeOption: 0,
                name: '',                
                email: '',
                password: ''
            }
        },
        methods: {

            // Listamos los usuarios

            listUsers(page, search, criterion){
                var m = this;
                this.Processing(1);
                var url = '/users?page=' + page + '&search=' + search + '&criterion=' + criterion;
                axios.get(url).then(function (response) {
                    var answer = response.data; 
                    m.users    = answer.users.data;
                    m.pagination    = answer.pagination;
                    delete m.pagination.data;                   
                }).catch(function (error) {
                    m.DisplayErrors(error);
                });
            },

            // Abrir el Modal

            openModal(model, action, data = []){
                switch(model){
                    case "user": {

                        switch(action){

                            case 'register':{
                                this.modal          =   1;
                                this.titleModal     =   'Crear nuevo usuario';
                                this.name           =   '';
                                this.email          =   '';
                                this.password       =   '';
                                this.typeOption     =   1;
                                break;
                            }

                            case 'update':{
                                this.modal          =   1;
                                this.titleModal     =   'Actualizar usuario';
                                this.id             =   data['id'];
                                this.name           =   data['name'];
                                this.email          =   data['email'];                                
                                this.typeOption     =   2;
                                break;
                            }
                        }
                    }
                }
            },

            // Cerrar modal

            closeModal(){
                this.modal          =   0;
                this.titleModal     =   '';
                this.name           =   '';
                this.password       =   '';
                this.email          =   '';
            },

            // Agregar Usuario

            addUser(){
                if(this.Validate())  return;     

                var url = 'users/';

                const params = {
                    name:           this.name,
                    password:       this.password,
                    email:          this.email                    
                }

                var xe = this;

                var m = this;

                this.Processing(2);

                axios.post(url, params).then(response => {
                    let info = response.data;
                    if (info.info == "existe") {
                        toastr.remove();
                        toastr.error("", "El usuario ya existe");
                    } else {
                        toastr.success("info", response.data.info)
                        xe.closeModal();
                        xe.listUsers(1, '', 'name');
                    }
                }).catch(error => {
                    m.DisplayErrors(error);
                });
            },

            // Actualizar Usuario 

            updateUser(id){       
                if(this.Validate())  return;    
                var url = 'users/' + id;

                const params = {
                    id:             this.id,
                    name:           this.name,
                    email:          this.email
                }

                var xe = this;

                this.Processing(3);

                axios.put(url, params).then(response => {
                    let info = response.data;
                    if (info.info == "existe") {
                        toastr.warning("", "El usuario ya existe");
                    } else {
                        toastr.success("info", response.data.info);
                        xe.closeModal();
                        xe.listUsers(1, '', 'name');
                    }
                }).catch(error => {
                    m.DisplayErrors(error);
                });
            },

            // Eliminar usuarios 

            deleteUser(user){
                Swal.fire({
                    title: '¿Estás seguro de eliminar este usuario?',
                    text: '¡Esta acción no se puede revertir!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, Eliminar!',
                    cancelButtonText: 'No, Cancelar!'
                }).then((result) => {
                    if (result.value) {
                        var url = 'users/' + user;

                        axios.delete(url).then(response => {
                            this.listUsers(1, '', 'title');
                            toastr.success('Usuario eliminado');
                        }).catch(error => {
                            Swal.fire(
                                'Algo sucedió!',
                                'No se pudo eliminar este usuario',
                                'error'
                            )
                        });                     
                    }
                })
            },

            // procesamiento 

            Processing(status){
                toastr.remove();

                var msg = ''

                if(status == 1){
                    msg = 'CARGANDO INFORMACIÓN...'
                }

                else if(status == 2) {
                    msg = 'REGISTRANDO INFORMACIÓN...'
                }

                else if(status == 3) {
                    msg = 'ACTUALIZANDO INFORMACIÓN...'
                }

                else msg = 'ELIMINANDO REGISTRO...'

                toastr.info('Accion', msg)
            },

            // Validaciones 

            Validate() {
                this.errorMensaje = [];

                if (!this.IsNullOrEmpty(this.name)){
                    this.errorMensaje.push("Ingresa un Nombre");
                }                   

                if (!this.IsNullOrEmpty(this.email)) {
                    this.errorMensaje.push("Ingresa un correo electrónico");
                }

                if(!this.validEmail(this.email)){
                    this.errorMensaje.push("El correo electrónico no es valido");
                }

                if (this.errorMensaje.length > 0) {

                    var t = "";

                    this.errorMensaje.forEach(el => {
                        t = t + el + "</br>";
                    });

                    toastr.error('',t);
                    // swal({ html: true, title: "Error", text: t, type: "error" });
                }

                return this.errorMensaje.length ? 1 : 0;
            },

            // Evento de errores 

            DisplayErrors(error) {
                toastr.remove();
                toastr.options = {
                    timeOut: 10000
                };

                if (error.response.data.errors) {
                    this.validationErrors = error.response.data.errors;
                    var t = "";
                    $.each(this.validationErrors, function(key, value) {
                        t = t + value + "</br>";
                    });

                    if (this.IsNullOrEmpty(t)) toastr.error("info", t);

                    return;
                }

                if (error.response) {
                    toastr.error("Error de servidor, intenta nuevamente.");
                } else if (error.request) {
                    toastr.error("El servidor no responde, intente mas tarde.");
                } else {
                    toastr.error("Error inesperado, por favor intente nuevamente.");
                }
            },

            // Valida que sea un email

            validEmail: function(email) {
                var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                return re.test(email);
            },

            // Cambio de pagina

            changePage(page, search, criterion) {
                this.pagination.current_page = page;
                this.listUsers(page, search, criterion);
            },

            // 

            IsNullOrEmpty(t) {
                return (
                    void 0 !== t && null != t && 0 != /\S/.test(t) && t.toString().trim()
                );
            },

            since: function(d){
                return moment(d).fromNow();
            },

            moment: function (date) {
              return moment(date);
            },

            date: function (date) {
              return moment(date).format('D MMMM YYYY');
            }
        },

        computed: {
            isActived: function() {
                return this.pagination.current_page;
            },

            pagesNumber: function() {
                if(!this.pagination.to){
                    return [];
                }

                var from = this.pagination.current_page - this.offset; 
                if(from < 1){
                    from = 1;
                }

                var to = from + (this.offset * 2); 
                if(to >= this.pagination.last_page){
                    to = this.pagination.last_page;
                }

                var pagesArray = [];
                while(from <= to){
                    pagesArray.push(from);
                    from++;
                }

                return pagesArray;
            }
        },

        mounted(){
            this.listUsers(1, this.search, this.criterion);
        }
    }
</script>

