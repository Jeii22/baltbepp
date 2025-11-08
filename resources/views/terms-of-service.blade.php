@extends('layouts.app')

@section('content')
<div class="py-10">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white shadow-sm sm:rounded-xl overflow-hidden">
            <div class="p-6 md:p-10 prose prose-blue max-w-none">
                <h1>Terms and Conditions</h1>
                <p class="text-gray-500">Last updated: November 8, 2025</p>

                <p>Welcome to Balt-Bep Ferries. These Terms and Conditions ("Terms") govern your access to and use of our website, booking services, and related features (collectively, the "Services"). By accessing or using our Services, you agree to be bound by these Terms.</p>

                <h2>1. Acceptance of Terms</h2>
                <p>By creating an account, making a booking, or using any part of the Services, you acknowledge that you have read, understood, and agree to these Terms and our Privacy Policy. If you do not agree, please do not use the Services.</p>

                <h2>2. Eligibility and Account Responsibilities</h2>
                <ul>
                    <li>You must provide accurate and complete information during registration and keep it up to date.</li>
                    <li>You are responsible for safeguarding your account credentials and for all activities under your account.</li>
                    <li>Notify us immediately of any unauthorized use or security breach.</li>
                </ul>

                <h2>3. Bookings, Payments, and Refunds</h2>
                <ul>
                    <li>All bookings are subject to availability and our operational policies.</li>
                    <li>Payments processed via third-party providers (e.g., PayMongo/GCash, card processors) are subject to their terms.</li>
                    <li>Refunds and changes follow our published policies and any applicable law.</li>
                </ul>

                <h2>4. Acceptable Use</h2>
                <ul>
                    <li>Do not engage in any activity that disrupts or interferes with the Services or other users.</li>
                    <li>Do not attempt to gain unauthorized access to our systems or data.</li>
                    <li>Do not use the Services for unlawful purposes.</li>
                </ul>

                <h2>5. Personal Data and DPA 2012 Compliance</h2>
                <p>We are committed to protecting your personal data and complying with the Philippine Data Privacy Act of 2012 (Republic Act No. 10173), its Implementing Rules and Regulations (IRR), and guidance from the National Privacy Commission (NPC).</p>
                <p>By using our Services, you acknowledge and agree that we will collect and process your personal data in accordance with these Terms and our Privacy Policy, which forms an integral part of these Terms.</p>
                <p>Key commitments:</p>
                <ul>
                    <li><strong>Lawful, fair, and transparent processing</strong> for legitimate purposes related to booking, payment, and customer support.</li>
                    <li><strong>Data minimization</strong> and collection limited to what is necessary (e.g., name, contact details, travel details, payment references).</li>
                    <li><strong>Security measures</strong> including encryption in transit, access controls, logging, multi-factor authentication, and session protections.</li>
                    <li><strong>Data subject rights</strong> supported: the right to be informed, access, rectification, erasure, object, portability, and to lodge a complaint with the NPC.</li>
                    <li><strong>Third-party processing</strong> for payments, email, analytics, and security (e.g., reCAPTCHA v3) under appropriate safeguards and contracts.</li>
                    <li><strong>Retention</strong> only for as long as necessary for the purposes stated or as required by law, then secure deletion or anonymization.</li>
                </ul>

                <h2>6. Security</h2>
                <p>We employ reasonable and appropriate organizational, physical, and technical measures to protect personal data against unauthorized access, alteration, disclosure, or destruction. However, no method of transmission over the Internet or electronic storage is 100% secure; therefore, we cannot guarantee absolute security.</p>

                <h2>7. Intellectual Property</h2>
                <p>All content, trademarks, logos, and other intellectual property appearing in the Services are owned by us or our licensors. You may not reproduce, distribute, or create derivative works without prior written consent.</p>

                <h2>8. Service Availability and Changes</h2>
                <p>We may modify, suspend, or discontinue any part of the Services at any time with or without notice. We are not liable for any unavailability due to maintenance or factors beyond our control.</p>

                <h2>9. Limitation of Liability</h2>
                <p>To the maximum extent permitted by law, we will not be liable for any indirect, incidental, special, consequential, or punitive damages, or any loss of profits or revenues, arising out of or related to your use of the Services.</p>

                <h2>10. Indemnity</h2>
                <p>You agree to indemnify and hold us harmless from any claims, liabilities, damages, losses, and expenses, including legal fees, arising out of your violation of these Terms or misuse of the Services.</p>

                <h2>11. Changes to These Terms</h2>
                <p>We may update these Terms from time to time. We will post the updated Terms with a new “Last updated” date. Your continued use of the Services constitutes acceptance of the changes.</p>

                <h2>12. Contact and Data Protection Officer (DPO)</h2>
                <p>If you have questions about these Terms or our data protection practices, or wish to exercise your data subject rights, contact our Data Protection Officer (DPO):</p>
                <ul>
                    <li>Email: dpo@baltbep.example</li>
                    <li>Address: Balt-Bep Ferries, [Company Address]</li>
                </ul>

                <p>For more information on how we process personal data, please review our <a href="{{ route('privacy-policy') }}">Privacy Policy</a>.</p>
            </div>
        </div>
    </div>
</div>
@endsection