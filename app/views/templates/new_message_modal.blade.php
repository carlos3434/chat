<div id="newMessageModal" class="modal fade">
    <div class="modal-dialog">
        <form v-on:submit.prevent='sendConversation'>
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span></button>
                    <h4 class="modal-title">Nuevo Mensaje</h4>
                </div>
                <div class="modal-body">				
                    <div class="form-group">
                        <label>Areas</label>
                        <select class="form-control" v-model="area_id" @change="changeArea">
                            <option selected>Debe escoger una area primero</option>
                            <option v-for="area in areas" v-bind:value="area.id">@{{ area.nombre }}</option>
                        </select>
                        <label>Users</label>
                        <select class="form-control" v-model="users_id" @change="changeUser">
                            <option selected>Seleccione usuario</option>
                            <option v-for="user in users" v-bind:value="user.id">@{{ user.username }}</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Message</label>
                        <textarea @keyup.prevent="handleKeypressModal" id='new_message' v-model="body" rows="4" class="form-control"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <input class="btn btn-danger" type="submit" :disabled="body.trim()===''" value="Enviar">
                </div>
            </div>
        </form>
    </div>
</div>
