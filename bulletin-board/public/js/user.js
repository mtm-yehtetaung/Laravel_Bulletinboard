function showDeleteDetail(user) {
    $("#deleteId").val(user.id);
    $("#user-delete #user-id").text(user.id);
    $("#user-delete #user-name").text(user.name);
    if (user.type == "0") {
        $("#user-delete #user-type").text("Admin");
    } else {
        $("#user-delete #user-type").text("User");
    }
    $("#user-delete #user-email").text(user.email);
    $("#user-delete #user-phone").text(user.phone);
    $("#user-delete #user-dob").text(
        moment(user.dob).format("YYYY/MM/DD")
    );
    $("#user-delete #user-address").text(user.address);
}

function showUserDetail(user) {
    $("#user-detail #user-name").text(user.name);
    if (user.type == "0") {
        $("#user-detail #user-type").text("Admin");
    } else {
        $("#user-detail #user-type").text("User");
    }
    $("#user-detail #user-email").text(user.email);
    $("#user-detail #user-phone").text(user.phone);
    $("#user-detail #user-date-of-birth").text(
        moment(user.dob).format("YYYY/MM/DD")
    );
    $("#user-detail #user-address").text(user.address);
    $("#user-detail #user-created-at").text(
        moment(user.created_at).format("YYYY/MM/DD")
    );
    $("#user-detail #user-created-user").text(user.created_user);
    $("#user-detail #user-updated-at").text(
        moment(user.updated_at).format("YYYY/MM/DD")
    );
    $("#user-detail #user-updated-user").text(user.updated_user);
    $("#user-detail #user-profile").attr("src", `/storage/images/${user.profile}`);
}

