<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { login } from '@/routes';
import { store } from '@/routes/register';

const props = defineProps<{
    invitation?: {
        email: string;
        team_name: string;
        role: string;
        token: string;
    }
}>();

const isInvitation = !!props.invitation;
</script>

<template>
    <Head :title="isInvitation ? 'Complete Registration' : 'Register'" />

    <div class="flex min-h-screen flex-col items-center bg-muted p-6">
        <div class="w-full max-w-sm">
            <div class="bg-background rounded-lg border shadow-sm p-6">
                <div class="flex flex-col space-y-2 text-center mb-6">
                    <h1 class="text-2xl font-semibold tracking-tight">
                        {{ isInvitation ? 'Complete Registration' : 'Create an account' }}
                    </h1>
                    <p v-if="isInvitation" class="text-sm text-muted-foreground">
                        You've been invited to <strong>{{ invitation.team_name }}</strong>
                        as a <strong>{{ invitation.role === 'employee' ? 'Employee' : 'Contractor' }}</strong>
                    </p>
                    <p v-else class="text-sm text-muted-foreground">
                        Enter your details below to create your account
                    </p>
                </div>

                <!-- Форма для обычной регистрации -->
                <Form
                    v-if="!isInvitation"
                    v-bind="store.form()"
                    :reset-on-success="['password', 'password_confirmation']"
                    v-slot="{ errors, processing }"
                    class="flex flex-col gap-4"
                >
                    <div class="grid gap-2">
                        <Label for="company_name">Company name</Label>
                        <Input id="company_name" name="company_name" type="text" required autofocus />
                        <InputError :message="errors.company_name" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="name">Name</Label>
                        <Input id="name" name="name" type="text" required />
                        <InputError :message="errors.name" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="email">Email</Label>
                        <Input id="email" name="email" type="email" required />
                        <InputError :message="errors.email" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="password">Password</Label>
                        <PasswordInput id="password" name="password" required />
                        <InputError :message="errors.password" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="password_confirmation">Confirm password</Label>
                        <PasswordInput id="password_confirmation" name="password_confirmation" required />
                    </div>

                    <Button type="submit" class="mt-2 w-full" :disabled="processing">
                        <Spinner v-if="processing" />
                        Create account
                    </Button>
                </Form>

                <!-- Форма для регистрации по приглашению -->
                <Form
                    v-else
                    :action="`/invitations/register/${invitation.token}`"
                    method="post"
                    class="flex flex-col gap-4"
                    v-slot="{ errors, processing }"
                >
                    <div class="grid gap-2">
                        <Label for="name">Name</Label>
                        <Input id="name" name="name" type="text" required autofocus />
                        <InputError :message="errors.name" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="email">Email</Label>
                        <div class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 p-2">
                            {{ invitation.email }}
                        </div>
                    </div>

                    <div class="grid gap-2">
                        <Label for="password">Password</Label>
                        <PasswordInput id="password" name="password" required />
                        <InputError :message="errors.password" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="password_confirmation">Confirm password</Label>
                        <PasswordInput id="password_confirmation" name="password_confirmation" required />
                    </div>

                    <Button type="submit" class="mt-2 w-full" :disabled="processing">
                        <Spinner v-if="processing" />
                        Complete registration
                    </Button>
                </Form>

                <div v-if="!isInvitation" class="text-center text-sm text-muted-foreground mt-4">
                    Already have an account?
                    <TextLink :href="login()" class="underline underline-offset-4">
                        Log in
                    </TextLink>
                </div>
            </div>
        </div>
    </div>
</template>
