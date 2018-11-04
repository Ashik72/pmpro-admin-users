jQuery(document).ready(function($) {

    var loadJS = {

        init : function() {
            console.log("dsf");
            this.users_search_page();
            this.changeTitles();
        },

        users_search_page: function () {

            //$(".search-box").parent("form").attr("action", "users.php?page=users-custom");
            if (!users_custom.is_super_admin)
                $('.wp-first-item [href="users.php"]').css("cssText", "display: none !important;");

        },

        changeTitles: function () {


            if ($(".setpass #pass1").length > 0) {

                $(".wp-generate-pw").trigger("click");
                $(".setpass #pass1").attr( 'type', 'text');

                $(".setpass #pass1").val($("#pass1").data('pw'))
                $(".setpass #pass1").attr( 'value' , $("#pass1").data('pw'))
                $(".setpass .wp-hide-pw").css("display", "none")

            }


            if (users_custom.is_super_admin) return;

            // $.getScript( users_custom.admin_dir+"js/user-profile.js", function( data, textStatus, jqxhr ) {
            //
            //
            // });

            $('[href="users.php"] .wp-menu-name').text("My Team");
            $('[href="users.php?page=users-custom"]').text("Team Members");
            $('[href="user-new.php"]').text("Add Member");
            $('[href="profile.php"]').css("display", "none");

            $('#add-existing-user').css("display", "none");
            $('#adduser').css("display", "none");
            $('#add-existing-user + p').css("display", "none");
            $('h2#create-new-user').css("display", "none");

            console.log($("#role").parent("td"));
            $("#role").parent("td").parent("tr").css('display', 'none');
            $("#role").val("editor");
            $('[href="users.php"]').attr("href", "users.php?page=users-custom");

        }



        };


    loadJS.init();



});
