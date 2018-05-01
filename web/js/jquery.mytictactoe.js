'use strict';

(function ($) {
    $.widget("jarenal.myTicTacToe", {
        options: {
            status: 'stopped',
            playerUnit: 'X',
            stats: {wins: 0, ties: 0, losts: 0}
        },
        _create: function () {
            var self = this;
            $.Mustache.load('./js/templates/mytictactoe.mustache')
                .done(function () {
                    self.element.mustache('mytictactoe', {});

                    // On Click Start
                    self.element.on('click', '#btn-start', function (e) {
                        e.preventDefault();
                        console.log('Start Running!!!');
                        self._trigger('running', null, {});
                    })

                    // On Click Reset
                    self.element.on('click', '#btn-reset', function (e) {
                        e.preventDefault();
                        console.log('Reset game!');
                        self._trigger('stopped', null, {});
                    });
                });

            // On Running
            this.element.on('mytictactoerunning', function (e) {
                self.options.status = 'running';
                self.options.playerUnit = $('#playerUnit').val();
                self._cleanBoard();
                $('#toolbar-stopped').hide();
                $('#toolbar-playing').show();
            });

            // On Stopped
            this.element.on('mytictactoestopped', function (e) {
                self.options.status = 'stopped';
                $('#toolbar-playing').hide();
                $('#toolbar-stopped').show();
                self._trigger('reloadStats', null, {});
            });

            // On Reload Stats
            this.element.on('mytictactoereloadstats', function (e) {
                $('#wins-counter').html(self.options.stats.wins);
                $('#ties-counter').html(self.options.stats.ties);
                $('#losts-counter').html(self.options.stats.losts);
            });

            // On click a square
            this.element.on('click', '.square', function (e) {
                e.preventDefault();

                if (self.options.status === 'stopped') {
                    console.log('Game stopped!');
                    alert('Please, click start for to play.')
                    return false;
                }

                // Preventing to click an occupied square
                if ($(this).hasClass('player-X') || $(this).hasClass('player-O')) {
                    return false;
                }

                $(this).addClass('player-'+self.options.playerUnit);

                var winner = self._findWinner();
                console.log(winner, 'winner');
                if (winner) {
                    if (self.options.playerUnit === winner) {
                        self.options.stats.wins++;
                        alert('CPU says: Congrats! you were very lucky.');
                    } else {
                        self.options.stats.losts++;
                        alert('CPU says: Too much easy for me.');
                    }
                    self._trigger('stopped', null, {});
                    return false;
                }

                var totalFreeSquares = self._countFreeSquares();
                if (totalFreeSquares > 0) {
                    var params = {boardState: self._getBoardState(), player: self.options.playerUnit};

                    $.ajax('/api/move', {
                        method: 'POST',
                        cache: false,
                        data: params,
                        dataType: 'json',
                        error: function (jqXHR, textStatus, error) {
                            alert('Error on /api/move request');
                            console.log(error, 'Request error');
                        },
                        success: function (data, textStatus, jqXHR) {
                            console.log(data, 'Request success');

                            if (data.length === 3) {
                                var coords = data[0] + '-' + data[1];
                                $('[data-coords="' + coords + '"]').addClass('player-' + data[2]);
                            }

                            var winner = self._findWinner();
                            console.log(winner, 'winner');
                            if (winner) {
                                if (self.options.playerUnit === winner) {
                                    self.options.stats.wins++;
                                    alert('CPU says: Congrats! you were very lucky.');
                                } else {
                                    self.options.stats.losts++;
                                    alert('CPU says: Too much easy for me.');
                                }
                                self._trigger('stopped', null, {});
                                return false;
                            }

                        }
                    });
                } else {
                    self.options.stats.ties++;
                    alert('CPU says: Nobody wins and nobody loses. Probably I\'m as smart as you.');
                    self._trigger('stopped', null, {});
                }
            });
        },
        _setOption: function( key, value ) {
            this.options[ key ] = value;
        },
        getStatus: function () {
            return this.options.status;
        },
        _getBoardState: function () {
            var squares = $('.square');
            var boardState = [[],[],[]];
            var counter = 0;
            var playerUnit;
            var row = 0;

            $.each(squares, function (index, element) {

                if (counter > 2) {
                    counter = 0;
                    row++;
                }

                if ($(element).hasClass('player-O')) {
                    playerUnit = 'O';
                } else if ($(element).hasClass('player-X')) {
                    playerUnit = 'X';
                } else {
                    playerUnit = '-';
                }
                boardState[row].push(playerUnit);

                counter++;
            });

            return boardState;
        },
        _cleanBoard: function () {
            var squares = $('.square');
            $.each(squares, function (index, element) {
                if ($(element).hasClass('player-X')) {
                    $(element).removeClass('player-X');
                } else if ($(element).hasClass('player-O')) {
                    $(element).removeClass('player-O');
                }
            });
        },
        _findWinner: function () {
            var board = this._getBoardState();
            var winner;
            var lines = [];

            lines.push([board[0][0],board[0][1],board[0][2]]);
            lines.push([board[1][0],board[1][1],board[1][2]]);
            lines.push([board[2][0],board[2][1],board[2][2]]);
            lines.push([board[0][0],board[1][0],board[2][0]]);
            lines.push([board[0][1],board[1][1],board[2][1]]);
            lines.push([board[0][2],board[1][2],board[2][2]]);
            lines.push([board[0][0],board[1][1],board[2][2]]);
            lines.push([board[0][2],board[1][1],board[2][0]]);

            $.each(lines, function (i, current) {
                if(current.every(function (t) {
                    return t === 'X';
                })) {
                    winner = 'X';
                    return false;
                }
            });

            $.each(lines, function (i, current) {
                if(current.every(function (t) {
                    return t === 'O';
                })) {
                    winner = 'O';
                    return false;
                }
            });

            return winner;

        },
        _countFreeSquares: function () {
            return $('.board').find(":not([class*='player'])").length;
        }

    });

})(jQuery);