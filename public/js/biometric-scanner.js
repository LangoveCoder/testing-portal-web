/**
 * Universal Biometric Scanner Service - Updated for SecuGen WebAPI
 * Supports: SecuGen (all models via HTTP API)
 */

class BiometricScanner {
    constructor() {
        this.isConnected = false;
        this.deviceType = null;
        this.deviceName = null;
        this.capturedData = null;
        this.qualityScore = 0;
        this.apiUrl = 'https://localhost:8000'; // SecuGen WebAPI port
    }

    /**
     * Initialize and detect available scanner
     */
    async initialize() {
        console.log('🔍 Initializing biometric scanner...');
        
        this.showStatus('Detecting scanner...', 'loading');
        
        try {
            // Test connection to SecuGen WebAPI
            const response = await fetch(`${this.apiUrl}/SGIFPCapture?Timeout=1000`, {
                method: 'GET',
                mode: 'no-cors' // Important for localhost cross-origin
            });

            // If we get here without error, service is running
            this.isConnected = true;
            this.deviceType = 'SECUGEN';
            this.deviceName = 'SecuGen Scanner';
            
            console.log(`✓ Scanner detected: ${this.deviceName}`);
            this.showStatus(`✓ Connected: ${this.deviceName}`, 'success');
            
            return {
                success: true,
                device: this.deviceName,
                type: this.deviceType,
                message: 'Scanner connected successfully'
            };
            
        } catch (error) {
            console.log('✗ Scanner connection failed:', error);
            this.showStatus('Connection failed', 'error');
            
            return {
                success: false,
                message: 'Scanner connection failed. Please ensure SgiBioSrv service is running on port 8000.'
            };
        }
    }

   async capture() {
    if (!this.isConnected) {
        return {
            success: false,
            message: 'Scanner not connected. Please connect first.'
        };
    }

    console.log(`📸 Capturing fingerprint...`);
    this.showStatus('Place finger on scanner...', 'loading');

    try {
        // Call SecuGen WebAPI capture endpoint
        const response = await fetch(`${this.apiUrl}/SGIFPCapture?Timeout=10000&Quality=50&TemplateFormat=ISO&ImageWSQRate=0.75`, {
            method: 'GET'
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const result = await response.json();

        console.log('Capture result:', result);

        if (result.ErrorCode === 0) {
            // Success!
            this.capturedData = {
                template: result.TemplateBase64,
                image: 'data:image/bmp;base64,' + result.BMPBase64,
                format: 'ISO'
            };
            this.qualityScore = result.ImageQuality || result.NFIQ * 20 || 75;

            this.showStatus(`✓ Captured (Quality: ${this.qualityScore}%)`, 'success');

            return {
                success: true,
                data: this.capturedData,
                quality: this.qualityScore,
                deviceInfo: {
                    manufacturer: result.Manufacturer,
                    model: result.Model,
                    serialNumber: result.SerialNumber,
                    width: result.ImageWidth,
                    height: result.ImageHeight,
                    dpi: result.ImageDPI
                },
                message: 'Fingerprint captured successfully'
            };
        } else {
            // Error from SecuGen API
            const errorMessages = {
                54: 'Timeout - Please place finger on scanner',
                51: 'System file load failure',
                52: 'Sensor initialization failed',
                55: 'Device not found',
                101: 'Very low minutiae count - Please scan again',
                10001: 'License error',
                10002: 'Invalid domain',
                10003: 'License expired',
                10004: 'WebAPI did not receive origin header from browser'
            };

            const errorMsg = errorMessages[result.ErrorCode] || `Error code: ${result.ErrorCode}`;

            this.showStatus('✗ Capture failed', 'error');

            return {
                success: false,
                message: errorMsg,
                errorCode: result.ErrorCode
            };
        }

    } catch (error) {
        console.error('Capture error:', error);
        this.showStatus('✗ Capture error', 'error');

        return {
            success: false,
            message: 'Capture failed: ' + error.message
        };
    }
}
    /**
     * Verify fingerprint against stored template
     */
    async verify(storedTemplate, liveTemplate) {
        console.log('🔍 Verifying fingerprint...');
        this.showStatus('Verifying...', 'loading');

        try {
            const response = await fetch(`${this.apiUrl}/SGIMatchScore`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    'Template1': storedTemplate,
                    'Template2': liveTemplate,
                    'TemplateFormat': 'ISO'
                })
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const result = await response.json();

            if (result.ErrorCode === 0) {
                const matchScore = result.MatchingScore;
                const threshold = 100; // Matching score threshold (0-199, 199 = identical)
                const isMatch = matchScore >= threshold;

                // Convert to percentage
                const scorePercent = Math.round((matchScore / 199) * 100);

                this.showStatus(isMatch ? `✓ Match (${scorePercent}%)` : '✗ No match', isMatch ? 'success' : 'error');

                return {
                    success: true,
                    match: isMatch,
                    score: scorePercent,
                    rawScore: matchScore,
                    message: isMatch ? 'Fingerprint matched' : 'Fingerprint did not match'
                };
            } else {
                return {
                    success: false,
                    match: false,
                    message: `Verification error: ${result.ErrorCode}`
                };
            }

        } catch (error) {
            console.error('Verification error:', error);
            this.showStatus('✗ Verification error', 'error');

            return {
                success: false,
                match: false,
                message: 'Verification failed: ' + error.message
            };
        }
    }

    /**
     * Show status message
     */
    showStatus(message, type) {
        const statusElement = document.getElementById('scanner_status');
        const indicatorElement = document.getElementById('scanner_indicator');

        if (statusElement) {
            statusElement.textContent = message;
        }

        if (indicatorElement) {
            indicatorElement.className = 'w-3 h-3 rounded-full';
            switch (type) {
                case 'success':
                    indicatorElement.classList.add('bg-green-500');
                    break;
                case 'error':
                    indicatorElement.classList.add('bg-red-500');
                    break;
                case 'loading':
                    indicatorElement.classList.add('bg-yellow-500', 'animate-pulse');
                    break;
                default:
                    indicatorElement.classList.add('bg-gray-400');
            }
        }
    }

    /**
     * Get captured data
     */
    getCapturedData() {
        return this.capturedData;
    }

    /**
     * Get quality score
     */
    getQualityScore() {
        return this.qualityScore;
    }

    /**
     * Reset
     */
    reset() {
        this.capturedData = null;
        this.qualityScore = 0;
    }

    /**
     * Disconnect
     */
    disconnect() {
        this.isConnected = false;
        this.deviceType = null;
        this.showStatus('Scanner disconnected', 'default');
    }
}

// Export for use
if (typeof module !== 'undefined' && module.exports) {
    module.exports = BiometricScanner;
}