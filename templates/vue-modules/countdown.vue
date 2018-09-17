<template>
    <div class="background" v-bind:style="style">
        <div id="timer">
            <div class="globalTitle">Предположительное время: 26 октября в 12:30</div>
            <div id="days" class="number">
                <span class="text">{{days}}</span>
                <span class="title">{{daysText}}</span>
            </div>
            <div id="hours" class="number">
                <span class="text">{{hours}}</span>
                <span class="title">{{hoursText}}</span>
            </div>
            <div id="minutes" class="number">
                <span class="text">{{minutes}}</span>
                <span class="title">{{minutesText}}</span>
            </div>
            <div id="seconds" class="number">
                <span class="text">{{seconds}}</span>
                <span class="title">{{secondsText}}</span>
            </div>
        </div>
    </div>
</template>

<script>
   import * as moment from 'moment'
    export default {
        name : "countdown",
        created: function () {
            console.log(this.date);
            this.updateTimer();
            setInterval(this.updateTimer, 1000);
        },
        data: function () {
            return {
                date : moment("2018-10-27 12:30:00"),
                days : 0,
                hours : 0,
                minutes : 0,
                seconds : 0,
            }
        },
        computed : {
            number : function () {
                let min = 1;
                let max = 3;
                return  Math.floor(Math.random() * (max - min + 1)) + min;
            },
            style : function () {
                return {
                    backgroundImage : `url(./assets/countdown/${this.number}.jpg)`
                }
            },
            daysText : function () {
                if ((this.days % 100 < 20 && this.days % 100 > 10) || this.days % 10 === 0 || this.days % 10 > 4)
                    return "дней";
                else if (this.days % 10 > 1 && this.days % 10 < 5)
                    return "дня";
                else return "день";
            },
            hoursText : function () {
                if ((this.hours % 100 < 20 && this.hours % 100 > 10) || this.hours % 10 === 0 || this.hours % 10 > 4)
                    return "часов";
                else if (this.hours % 10 > 1 && this.hours % 10 < 5)
                    return "часа";
                else return "час";
            },
            minutesText : function () {
                if ((this.minutes % 100 < 20 && this.minutes % 100 > 10) || this.minutes % 10 === 0 || this.minutes % 10 > 4)
                    return "минут";
                else if (this.minutes % 10 > 1 && this.minutes % 10 < 5)
                    return "минуты";
                else return "минуты";
            },
            secondsText : function () {
                if ((this.seconds % 100 < 20 && this.seconds % 100 > 10) || this.seconds % 10 === 0 || this.seconds % 10 > 4)
                    return "секунд";
                else if (this.seconds % 10 > 1 && this.seconds % 10 < 5)
                    return "секунды";
                else return "секунда";
            }

        },
        methods : {
            now : function () {
                return moment();
            },
            updateTimer : function () {
                const now = this.now();
                this.days = this.date.diff(now, "days");
                this.hours = this.date.diff(now, "hours") % 24;
                this.minutes = this.date.diff(now, "minutes") % 60;
                this.seconds = this.date.diff(now, "seconds") % 60;
            }
        }
    }
</script>

<style lang="scss">
    body, html {
        margin: 0;
        padding: 0;
        background-color: dimgray;
        font-family: Calibri, sans-serif;
    }
</style>

<style lang="scss" scoped>
    $gray : #eaeaea;
    .background {
        width: 100%;
        height: 100%;
        background-position: center center;
        background-size: 100% 100%;
        display: flex;
        align-items: center;

        #timer {
          margin: 0 auto;

            .globalTitle {
                text-align: center;
                width: 100%;
                font-size: 24px;
                color: $gray;
            }

        }

        .number {
            display: inline-block;
            text-align: center;
            font-size: 150px;
            color: $gray;
            max-width: 160px;
            margin: 0 20px;
            text-shadow: 0 0 25px rgba(50, 50, 50, 1);

            .text {
                clear: both;
            }

            .title {
                font-size: 32px;
                font-weight: bold;
                display: block;
                margin-top: -20px;
            }
        }
    }
</style>