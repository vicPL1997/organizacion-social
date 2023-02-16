function eliminarSede(id){
    Swal.fire({
        title: '¿Estás seguro que deseas eliminar esta sede ? Se eliminarán todos los proyectos dentro de la sede y también los usuarios asociados a dichos proyectos',
        showDenyButton: true,
        confirmButtonText: 'Eliminar',
        denyButtonText: `Conservar`,
    }).then((result) => {
        /* Read more about isConfirmed, isDenied below */
        if (result.isConfirmed) {
            Swal.fire('¡Se ha eliminado la sede correctamente!', '', 'success')
            var Ruta = Routing.generate('eliminar_sede');

            $.ajax({
                type: 'POST',
                url: Ruta,
                data: ({id: id}),
                async: true,
                dataType: "json",
                success: function (data){
                    window.location.reload();
                }
            })
        } else if (result.isDenied) {
            Swal.fire('No se ha eliminado la sede', '', 'error')
        }
    })
}

function eliminarProyecto(id){
    Swal.fire({
        title: '¿Estás seguro que deseas eliminar esta proyecto ? Se eliminarán todos los usuarios, voluntarios, empleados y gastos e ingresos asociados a dicho proyecto.',
        showDenyButton: true,
        confirmButtonText: 'Eliminar',
        denyButtonText: `Conservar`,
    }).then((result) => {
        /* Read more about isConfirmed, isDenied below */
        if (result.isConfirmed) {
            Swal.fire('¡Se ha eliminado el proyecto correctamente!', '', 'success')
            var Ruta = Routing.generate('eliminar_proyecto');

            $.ajax({
                type: 'POST',
                url: Ruta,
                data: ({id: id}),
                async: true,
                dataType: "json",
                success: function (data){
                    window.location.reload();
                }
            })
        } else if (result.isDenied) {
            Swal.fire('No se ha eliminado el proyecto', '', 'error')
        }
    })
}
function borrarUsuarioDelProyecto(idUsuario, idProyecto){
    Swal.fire({
        title: '¿Estás seguro que deseas eliminar el usuario del proyecto ?',
        showDenyButton: true,
        confirmButtonText: 'Eliminar',
        denyButtonText: `Conservar`,
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire('¡Se ha eliminado el usuario del proyecto correctamente!', '', 'success')
            var Ruta = Routing.generate('eliminar_usuario_proyecto');

            $.ajax({
                type: 'POST',
                url: Ruta,
                data: ({idUsuario: idUsuario, idProyecto: idProyecto}),
                async: true,
                dataType: "json",
                success: function (data){
                    document.getElementById(idUsuario).remove()
                }
            })
        } else if (result.isDenied) {
            Swal.fire('No se ha eliminado el usuario del proyecto', '', 'error')
        }
    })
}
function borrarGastoDelProyecto(idGasto, idProyecto){
    Swal.fire({
        title: '¿Estás seguro que deseas eliminar el gasto del proyecto ?',
        showDenyButton: true,
        confirmButtonText: 'Eliminar',
        denyButtonText: `Conservar`,
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire('¡Se ha eliminado el gasto del proyecto correctamente!', '', 'success')
            var Ruta = Routing.generate('eliminar_gasto_proyecto');

            $.ajax({
                type: 'POST',
                url: Ruta,
                data: ({idGasto: idGasto, idProyecto: idProyecto}),
                async: true,
                dataType: "json",
                success: function (data){
                    window.location.reload();
                }
            })
        } else if (result.isDenied) {
            Swal.fire('No se ha eliminado el gasto del proyecto', '', 'error')
        }
    })
}
function borrarIngresoDelProyecto(idIngreso, idProyecto){
    Swal.fire({
        title: '¿Estás seguro que deseas eliminar el ingreso del proyecto ?',
        showDenyButton: true,
        confirmButtonText: 'Eliminar',
        denyButtonText: `Conservar`,
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire('¡Se ha eliminado el ingreso del proyecto correctamente!', '', 'success')
            var Ruta = Routing.generate('eliminar_ingreso_proyecto');

            $.ajax({
                type: 'POST',
                url: Ruta,
                data: ({idIngreso: idIngreso, idProyecto: idProyecto}),
                async: true,
                dataType: "json",
                success: function (data){
                    window.location.reload();
                }
            })
        } else if (result.isDenied) {
            Swal.fire('No se ha eliminado el ingreso del proyecto', '', 'error')
        }
    })
}
function addUsuarioProyecto(idUsuario, idProyecto){
    var clicked2 = $('#'+idUsuario);

    if(clicked2.hasClass('changeActiveButton')){
        var clicked2 = $('#'+idUsuario);
        clicked2.removeClass('changeActiveButton');
        var Ruta = Routing.generate('eliminar_usuario_proyecto');

        $.ajax({
            type: 'POST',
            url: Ruta,
            data: ({idUsuario: idUsuario, idProyecto: idProyecto}),
            async: true,
            dataType: "json",
            success: function (data){
                //document.getElementById(idUsuario).remove()
            }
        })
    }else{
        clicked2.addClass('changeActiveButton');
        var Ruta = Routing.generate('add_user');

        $.ajax({
            type: 'POST',
            url: Ruta,
            data: ({idUsuario: idUsuario, idProyecto: idProyecto}),
            async: true,
            dataType: "json",
            success: function (data){
            }
        })
    }



}


