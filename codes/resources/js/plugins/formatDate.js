const formatDate = {
    formatSimpleDate(str) {
        if (!str) return;
        const monthNames = ["January", "February", "March", "April", "May", "June",
            "July", "August", "September", "October", "November", "December"
        ];
        let date = new Date(str);
        let day = date.getDate();
        let month = date.getMonth();
        let year = date.getFullYear();

        return day + ' ' + monthNames[month] + ', ' + year;
    },
    formatTime(str) {
        if (!str) return;
        let date = new Date(str);
        let hour = (date.getHours()).toString().padStart(2, '0');
        let min = (date.getMinutes()).toString().padStart(2, '0');

        return hour + ':' + min;
    },
    formatHourMinute(str) {
        if (!str) return;
        // Parse the time string
        let timeComponents = str.split(':');
        let hours = parseInt(timeComponents[0], 10);
        let minutes = parseInt(timeComponents[1], 10);

        // Determine whether it's morning or afternoon
        let meridiem = (hours < 12) ? 'AM' : 'PM';

        // Convert hours to 12-hour format
        hours = (hours > 12) ? hours - 12 : hours;
        hours = (hours === 0) ? 12 : hours; // Handle midnight (00:00)

        hours = hours.toString().padStart(2, '0');
        // Format the time
        return hours + ':' + ('0' + minutes).slice(-2) + ' ' + meridiem;

    },
    formDateTime(str) {
        const date = this.formatSimpleDate(str);
        const time = this.formatTime(str);

        return date + '<br> at ' + time;
    },
    timeAgo(str) {
        const date = new Date(str);

        const seconds = Math.floor((new Date() - date) / 1000);

        let interval = Math.floor(seconds / 31536000);
        if (interval > 1) {
            return interval + ' years ago';
        }

        interval = Math.floor(seconds / 2592000);
        if (interval > 1) {
            return interval + ' months ago';
        }

        interval = Math.floor(seconds / 86400);
        if (interval > 1) {
            return interval + ' days ago';
        }

        interval = Math.floor(seconds / 3600);
        if (interval > 1) {
            return interval + ' hours ago';
        }

        interval = Math.floor(seconds / 60);
        if (interval > 1) {
            return interval + ' minutes ago';
        }

        if (seconds < 10) return 'just now';

        return Math.floor(seconds) + ' seconds ago';
    }
};
export default formatDate;