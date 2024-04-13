######################################
# Mercado Pago Payment Gateway
######################################

This README file explains the functionality of the Mercado Pago payment gateway integration within the Secretario Virtual application.

## Overview

The Mercado Pago payment gateway integration allows users to subscribe to Secretario Virtual services and make payments securely through Mercado Pago. The integration involves several steps, including user creation, subscription plan selection, payment processing, and user authentication.

## Usage Create Flow

1. **User Registration and Subscription Plan Selection:**
   - Users sign up for Secretario Virtual services and select a subscription plan.
   - The application sends a request to the `user/create` endpoint to create the user and obtain the Mercado Pago subscription plan details.

2. **Payment Processing:**
   - After user creation and plan association, the application generates the Mercado Pago payment URL.
   - Users are redirected to the payment page on Mercado Pago to complete the subscription payment process.

3. **Payment Approval:**
   - Once payment is successfully processed on Mercado Pago, users are redirected back to the Secretario Virtual application.
   - Users are authenticated and marked as pending payment approval until payment confirmation is received.


## --------------------------------------

