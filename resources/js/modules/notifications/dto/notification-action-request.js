
export class NotificationActionRequest {
    constructor({ notificationId, action, reason = null, payloadId = null, payload = 'wallet' }) {
        this.notificationId = notificationId;
        this.action = action;
        this.reason = reason;
        this.payloadId = payloadId;
        this.payload = payload;
    }
}
