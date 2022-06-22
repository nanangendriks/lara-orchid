<?php

namespace App\Orchid\Screens;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Mail;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Menu;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Quill;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;

class EmailSenderScreen extends Screen
{
    /**
     * Query data.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [];
    }

    /**
     * Display header name.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Email sender';
    }

    public function description(): ?string
    {
        return "Tool that send ad-hoc email messages";
    }

    /**
     * Button commands.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Button::make('Send message')
                ->icon('paper-plane')
                ->method('sendMessage')
        ];
    }

    /**
     * Views.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            Layout::rows([
                Input::make('subject')
                    ->title('Subject')
                    ->required()
                    ->placeholder('Message subject line')
                    ->help('Enter the subject line for your messagge'),

                Relation::make('users.')
                    ->title('Recipients')
                    ->multiple()
                    ->required()
                    ->placeholder('Email address')
                    ->help('Enter the users that you would like to send this message to')
                    ->fromModel(User::class, 'name', 'email'),

                Quill::make('content')
                    ->title('content')
                    ->required()
                    ->placeholder('Insert text here')
                    ->help('Add the content for the message that you would like to send')
            ])
        ];
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'subject' => 'required|min:6|max:50',
            'users' => 'required',
            'content' => 'required|min:10'
        ]);

        Mail::raw($request->content, function (Message $message) use ($request) {
            $message->from('noreply@app.com');
            $message->subject($request->subject);

            foreach ($request->users as $email) {
                $message->to($email);
            }
        });

        Alert::info('Your email message has ben sent successfully');
    }
}
