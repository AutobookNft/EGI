// uemClientService.ts
// resources/ts/services/uemClientService.ts
import { ServerErrorResponse } from "../config/appConfig"; // Assumendo che ServerErrorResponse sia definita lì o in global.d.ts

export const UEM_Client_TS_Placeholder = {
    handleServerErrorResponse: (errorData: ServerErrorResponse, fallbackMessage: string = 'An error occurred.') => {
        console.error('UEM Server Error:', errorData);
        if (window.Swal && errorData.message) {
            window.Swal.fire({ icon: 'error', title: 'Server Error', text: errorData.message, confirmButtonColor: '#3085d6' });
        } else {
            alert(errorData.message || fallbackMessage);
        }
    },
    handleClientError: (errorCode: string, context: object = {}, originalError?: Error, userMessage?: string) => {
        console.error(`UEM Client Error [${errorCode}]:`, context, originalError);
        const displayMessage = userMessage || `Client error: ${errorCode}. See console.`;
        if (window.Swal) {
            window.Swal.fire({ icon: 'error', title: 'Application Error', text: displayMessage, confirmButtonColor: '#3085d6' });
        } else {
            alert(displayMessage);
        }
    }
};