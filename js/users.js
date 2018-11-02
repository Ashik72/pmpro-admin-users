jQuery(document).ready(function($) {

    var loadJS = {

        init : function() {
            console.log("dsf");
            this.users_search_page();
        },

        users_search_page: function () {

            //$(".search-box").parent("form").attr("action", "users.php?page=users-custom");
            if (!users_custom.is_super_admin)
                $('.wp-first-item [href="users.php"]').css("cssText", "display: none !important;");

        }


    };


    loadJS.init();



});
