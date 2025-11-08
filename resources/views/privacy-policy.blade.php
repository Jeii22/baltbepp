@extends('layouts.app')

@section('content')
<div class="py-10">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white shadow-sm sm:rounded-xl overflow-hidden">
            <div class="p-6 md:p-10 prose prose-blue max-w-none">
                <h1>Privacy Policy</h1>
                <p class="text-gray-500">Last updated: November 8, 2025</p>

                <p>This Privacy Policy explains how Balt-Bep Ferries ("we", "us", or "our") collects, uses, discloses, and protects your personal information in connection with our website and services (the "Services"). We are committed to complying with the Philippine Data Privacy Act of 2012 (Republic Act No. 10173), its IRR, and guidance from the National Privacy Commission (NPC).</p>

                <h2>1. Information We Collect</h2>
                <ul>
                    <li><strong>Account and Identity</strong>: name, email address, contact number.</li>
                    <li><strong>Transaction</strong>: booking details, travel preferences, payment references (we do not store full card data).</li>
                    <li><strong>Security & Login</strong>: IP address, device/OS/browser info, login attempts, 2FA verification data.</li>
                    <li><strong>Support</strong>: messages, feedback, attachments you provide.</li>
                    <li><strong>Usage</strong>: pages visited, actions performed, timestamps (via logs and analytics).</li>
                </ul>

                <h2>2. Legal Bases for Processing</h2>
                <ul>
                    <li><strong>Contract</strong>: to create and manage your account, process bookings, provide customer support.</li>
                    <li><strong>Legal obligation</strong>: to comply with financial, tax, and regulatory requirements.</li>
                    <li><strong>Legitimate interests</strong>: to secure the Services, prevent fraud, improve user experience, and maintain service quality.</li>
                    <li><strong>Consent</strong>: for optional features like marketing communications (which you can withdraw at any time).</li>
                </ul>

                <h2>3. How We Use Your Information</h2>
                <ul>
                    <li>To process bookings, payments, and send confirmations/alerts.</li>
                    <li>To secure accounts through MFA/2FA and session management.</li>
                    <li>To detect, prevent, and investigate fraud or misuse.</li>
                    <li>To respond to inquiries and support requests.</li>
                    <li>To analyze usage and improve the Services.</li>
                </ul>

                <h2>4. Sharing and Disclosure</h2>
                <p>We may share information with:</p>
                <ul>
                    <li><strong>Payment processors</strong> (e.g., PayMongo/GCash) for secure transaction handling.</li>
                    <li><strong>Email and communication providers</strong> for delivering verification and service messages.</li>
                    <li><strong>Security services</strong> (e.g., reCAPTCHA v3) to combat abuse and automated attacks.</li>
                    <li><strong>Service providers</strong> who process data on our behalf under contractual safeguards.</li>
                    <li><strong>Regulatory and law enforcement authorities</strong> when required by applicable law.</li>
                </ul>

                <h2>5. Data Retention</h2>
                <p>We retain personal data only for as long as necessary to fulfill the purposes described above or as required by law. After retention periods, we securely delete or anonymize data.</p>

                <h2>6. Security Measures</h2>
                <p>We employ reasonable and appropriate organizational, physical, and technical measures, including encryption in transit, role-based access controls, MFA/2FA, session protections (secure, HttpOnly, SameSite cookies), and activity logging. While we take safeguards seriously, no system is completely secure.</p>

                <h2>7. Your Rights</h2>
                <p>Subject to applicable law, you have the right to:</p>
                <ul>
                    <li>Be informed about personal data processing activities.</li>
                    <li>Access the personal data we hold about you.</li>
                    <li>Request correction of inaccurate or incomplete data.</li>
                    <li>Request deletion or object to processing in certain circumstances.</li>
                    <li>Data portability where applicable.</li>
                    <li>Lodge a complaint with the National Privacy Commission.</li>
                </ul>
                <p>To exercise these rights, please contact our DPO using the details below. We may require additional information to validate identity and scope.</p>

                <h2>8. International Transfers</h2>
                <p>Some service providers may be located outside the Philippines. Where applicable, we implement safeguards to ensure an adequate level of protection consistent with DPA requirements.</p>

                <h2>9. Cookies and Similar Technologies</h2>
                <p>We use cookies to enable core functionality (authentication, security), and to improve performance. You can control cookies via your browser settings, but disabling them may affect site functionality. Security-related cookies (e.g., for MFA) are essential.</p>

                <h2>10. Children’s Privacy</h2>
                <p>Our Services are not directed to children under the age of 13. If you believe we have collected personal information from a child, contact us so we can take appropriate action.</p>

                <h2>11. Changes to This Policy</h2>
                <p>We may update this Privacy Policy from time to time. The latest version will be posted here with the updated date. Material changes may be communicated via email or in-app notice.</p>

                <h2>12. Contact and Data Protection Officer (DPO)</h2>
                <ul>
                    <li>Email: dpo@baltbep.example</li>
                    <li>Address: Balt-Bep Ferries, [Company Address]</li>
                </ul>

                <p>If you have any concerns about our data protection practices, please contact us first. You may also contact the National Privacy Commission (NPC) for unresolved concerns.</p>
            </div>
        </div>
    </div>
</div>
@endsection