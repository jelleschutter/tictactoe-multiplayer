var turnTimer;
var snackbarTimer;

$(document).ready(function () {
    checkTurn(true);
    loadGame();
});


function checkTurn(output) {
    if (output === undefined) {
        output = false;
    }
    clearTimeout(turnTimer);
    $.post("../API/Turn",
        {
            game: getCurrentPageName()
        },
        function (data, status) {
            var turnData = JSON.parse(data);
            var turn = turnData.turn;
            if(turn) {
                showMessage(turnData.text);
                turnOnMove();
                loadGame();
            } else {
                if(output) {
                    showMessage(turnData.text);
                }
                turnTimer = setTimeout(function () {
                    checkTurn();
                }, 1000);
            }
        }
    );
}

function turnOnMove() {
    $("div.box").off("click");
    $("div.box").on("click",function () {
        move(this);
    });
}

function turnOffMove() {
    $("div.box").off("click");
    $("div.box").on("click",function () {
        showMessage("It's not your turn!");
    });
}

function setField() {
    $.post("../API/Field",
        {
            game: getCurrentPageName()
        },
        function (data, status) {
            var fieldData = JSON.parse(data);
            var field = fieldData.field;
            for (row = 0; row < 3; row++) {
                for (col = 0; col < 3; col++) {
                    $("div.field div.row[data-fieldrow=" + (row + 1) + "] div.box[data-fieldcol=" + (col + 1) + "]").removeClass("blue");
                    $("div.field div.row[data-fieldrow=" + (row + 1) + "] div.box[data-fieldcol=" + (col + 1) + "]").removeClass("red");
                    if (field[row][col] != 0) {
                        $("div.field div.row[data-fieldrow=" + (row + 1) + "] div.box[data-fieldcol=" + (col + 1) + "]").addClass(numToColor(field[row][col]));
                    }
                }
            }
        }
    );
}

function move(box) {
    $.post("../API/Move",
        {
            game: getCurrentPageName(),
            fieldrow: $(box).closest("div.row").attr("data-fieldrow"),
            fieldcol: $(box).attr("data-fieldcol")
        },
        function (data, status) {
            if (status == "success") {
                setField();
                var dataJSON = JSON.parse(data);
                var code = dataJSON.code;
                if (code == 1000 || code == 999) {
                    showWin();
                } else {
                    checkTurn();
                }
            } else {
                showMessage("Connection lost!");
            }
        }

    );
}

function loadGame() {
    $.post("../API/State",
        {
            game: getCurrentPageName()
        },
        function (data, status) {
            var stateData = JSON.parse(data);
            var state = stateData.state;

            switch (state) {
                case 1:
                    setField();
                break;

                case 2:
                    showWin();
                    setField();
                break;
            }
        }
    );
}

function showWin() {
    $(".end-game").removeClass("red");
    $(".end-game").removeClass("blue");
    $(".end-game").removeClass("draw");

    $.post("../API/Winner",
        {
            game: getCurrentPageName()
        },
        function (data, status) {
            var winnerData = JSON.parse(data);
            if (winnerData.type == 1) {
                $(".end-game").addClass(numToColor(winnerData.playernr));
                $(".end-game span.winner").text(winnerData.username);
            } else {
                $(".end-game").addClass("draw");
                $(".end-game span.winner").text("Nobody");
            }

            $(".end-game button").click(function () {
                restartGame();
            });

            $(".end-game").addClass("display");
        }
    );
}

function restartGame() {
    $.post("../API/Restart",
        {
            game: getCurrentPageName()
        },
        function (data, status) {
            location.reload(true);
        }
    );
}

function numToColor(number) {
    switch (number) {
        case 0:
            return "none";
            break;
        case 1:
            return "blue";
            break;
        case 2:
            return "red";
            break;
    }
}

function getCurrentPageName() {
    var url = window.location.href;
    var index = url.lastIndexOf("/") + 1;
    var filename = url.substr(index);
    return filename;
}

function showMessage(msg) {

    clearTimeout(snackbarTimer);

    $("#snackbar").text(msg);
    $("#snackbar").addClass("show");

    snackbarTimer = setTimeout(function () {
        $("#snackbar").removeClass("show");
    }, 3000);
}
