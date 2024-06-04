@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <!-- Profile Information Section -->
        <div class="col-md-3">
            <div class="profile-info-container" style="border: 1px solid #ccc; padding: 10px;">
                <h3>Profile Information:</h3>
                <div id="profile-info">
                    <p>Select a user to view their profile information.</p>
                </div>
            </div>
        </div>
        <!-- Message History Section -->
        <div class="col-md-6">
            <div class="messages-container" style="border: 1px solid #ccc; padding: 10px; max-height: 400px; overflow-y: scroll;">
                <h1 class="mb-4">Message History:</h1>
                <ul id="message-list" class="message-list" style="list-style-type: none; padding: 0;">
                </ul>
            </div>
        </div>
        <!-- User List and Message Form Section -->
        <div class="col-md-3">
            <div class="message-form-container" style="border: 1px solid #ccc; padding: 10px;">
                <div class="user-list">
                    <h3>User List:</h3>
                    <div class="input-group mb-2">
                        <input type="text" id="search-user" class="form-control" placeholder="Search user...">
                    </div>
                    <ul class="list-group" id="user-list">
                        @foreach ($users as $user)
                            @if ($user->id !== auth()->id())
                                <li class="list-group-item d-flex justify-content-between align-items-center user-link" data-id="{{ $user->id }}">
                                    <div class="user-info">
                                        @if ($user->photo)
                                            <img src="{{ asset('storage/' . $user->photo) }}" alt="User Photo" class="profile-photo mr-3" style="width: 50px; height: 50px; border-radius: 50%;">
                                        @else
                                            <div class="no-photo-placeholder mr-3" style="width: 50px; height: 50px; border-radius: 50%; background-color: #f0f0f0; display: flex; justify-content: center; align-items: center;">
                                                <span>No photo</span>
                                            </div>
                                        @endif
                                        <div>
                                            <h5 class="mb-0">{{ $user->name }}</h5>
                                            @if (isset($unreadMessagesCounts[$user->id]) && $unreadMessagesCounts[$user->id] > 0)
                                                <span id="unread-messages-{{ $user->id }}" class="unread-messages" style="color: red;">{{ $unreadMessagesCounts[$user->id] }} new messages</span>
                                            @endif
                                        </div>
                                    </div>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </div>
                <form id="message-form" method="post" action="{{ route('messages.send') }}" class="message-form">
                    @csrf
                    <input type="hidden" name="receiver_id" id="receiver_id" value="">
                    <div class="form-group mt-3">
                        <label for="message_content">Message (max 100 words) :</label>
                        <textarea name="message_content" id="message_content" class="form-control"></textarea>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <button type="submit" class="btn btn-warning">Send</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <a href="{{ route('home') }}" class="btn btn-primary mt-3">Go Back</a>

</div>
<style>
    .message-details p {
        margin: 0;
        word-wrap: break-word;
    }

    .message-details {
        max-width: 40%;
    }

    .profile-photo {
        object-fit: cover;
    }

    .user-list .active {
        background-color: #f0f0f0;
    }

    .user-list {
        max-height: 200px;
        overflow-y: auto;
    }
</style>

<script src="https://js.pusher.com/7.0/pusher.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {

    async function updateUnreadMessagesCount(userId, count) {
    try {
        const response = await fetch(`/messages/${userId}/unread-count`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ unread_count: count })
        });

        if (!response.ok) {
            console.error('Failed to update unread messages count');
        }
    } catch (error) {
        console.error('Error updating unread messages count:', error);
    }
}


        const urlParams = new URLSearchParams(window.location.search);
        const urlUserId = urlParams.get('id');
        let message = urlParams.get('message');

        if (message) {
            message = decodeURIComponent(message.replace(/\+/g, ' '));
        }

        if (urlUserId) {
            const selectedUser = document.querySelector('.user-link[data-id="' + urlUserId + '"]');
            if (selectedUser) {
                document.querySelectorAll('.user-link').forEach(user => user.classList.remove('active'));
                selectedUser.classList.add('active');
                document.getElementById('receiver_id').value = urlUserId;
                displayUserProfile(urlUserId);
                fetchAndDisplayMessages(urlUserId, true);
                selectedUser.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }

        function handleUserLinkClick(event) {
        event.preventDefault();
        document.querySelectorAll('.user-link').forEach(user => user.classList.remove('active'));
        this.classList.add('active');
        const userId = this.getAttribute('data-id');
        document.getElementById('receiver_id').value = userId;
        displayUserProfile(userId);
        fetchAndDisplayMessages(userId, true);
        updateUnreadMessagesCount(userId, 0); 


        const unreadMessagesSpan = document.getElementById('unread-messages-' + userId);
        if (unreadMessagesSpan) {
            unreadMessagesSpan.remove();
        }
        }

        const userLinks = document.querySelectorAll('.user-link');
        userLinks.forEach(link => {
            link.addEventListener('click', handleUserLinkClick);
        });

        if (message) {
            document.getElementById('message_content').value = message;
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const userId = '{{ auth()->id() }}';

        initializeUserLinks();

        const initialUserLink = document.querySelector('.user-link.active');
        if (initialUserLink) {
            const selectedUserId = initialUserLink.getAttribute('data-id');
            fetchAndDisplayMessages(selectedUserId, true);
        } else {
            document.getElementById('message-list').innerHTML = '<li class="no-messages-found">Select a user</li>';
        }

        setInterval(async function() {
            const activeUserLink = document.querySelector('.user-link.active');
            const selectedUserId = activeUserLink ? activeUserLink.getAttribute('data-id') : null;

            if (selectedUserId) {
                await fetchAndDisplayMessages(selectedUserId);
            }
        }, 1000);

        const messageForm = document.getElementById('message-form');
        messageForm.addEventListener('submit', async function(event) {
            event.preventDefault();
            const messageContent = document.getElementById('message_content').value.trim();
            const receiverId = document.getElementById('receiver_id').value;

            if (messageContent !== '') {
                try {
                    await sendMessage(messageContent, receiverId);
                } catch (error) {
                    console.error('Error sending message:', error);
                }
            }
        });

        async function sendMessage(messageContent, receiverId) {
            try {
                const response = await fetch('{{ route('messages.send') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        message_content: messageContent,
                        receiver_id: receiverId
                    })
                });

                if (response.ok) {
                    await fetchAndDisplayMessages(receiverId, true);
                    document.getElementById('message_content').value = '';
                } else {
                    console.error('Failed to send message');
                }
            } catch (error) {
                console.error('Error sending message:', error);
            }
        }

        async function fetchAndDisplayMessages(receiverId, scrollToLast = false) {
            try {

                const response = await fetch(`/messages/${receiverId}`);
                const data = await response.json();
                const messageList = document.getElementById('message-list');

                messageList.innerHTML = '';
                const relevantMessages = data.filter(message => message.sender_id == userId || message.receiver_id == userId);

                if (relevantMessages.length > 0) {
                    relevantMessages.forEach((message, index) => {
                        const listItem = document.createElement('li');
                        listItem.className = 'message-item mb-3';
                        listItem.style.clear = 'both';

                        const messageDetails = document.createElement('div');
                        messageDetails.className = 'message-details p-2';
                        messageDetails.style.borderRadius = '10px';

                        const senderName = userId === message.sender_id ? '{{ auth()->user()->name }}' : message.sender_name;

                        if (message.receiver_id == userId) {
                            messageDetails.style.backgroundColor = '#F0F0F0';
                            messageDetails.style.float = 'left';
                            const sender = document.createElement('strong');
                            sender.textContent = senderName;
                            messageDetails.appendChild(sender);
                        } else if (message.sender_id == userId) {
                            messageDetails.style.backgroundColor = '#DCF8C6';
                            messageDetails.style.float = 'right';
                            const sender = document.createElement('strong');
                            sender.textContent = 'You';
                            messageDetails.appendChild(sender);
                        }

                        const messageContent = document.createElement('p');
                        messageContent.textContent = message.message_content;
                        const messageTime = document.createElement('small');
                        messageTime.className = 'text-muted';
                        messageTime.textContent = new Date(message.created_at).toLocaleString();
                        messageDetails.appendChild(messageContent);
                        messageDetails.appendChild(messageTime);

                        if (index === relevantMessages.length - 1 && message.sender_id == userId && message.is_read === 1) {
                            const readAtMessage = document.createElement('small');
                            readAtMessage.className = 'text-muted';
                            readAtMessage.innerHTML = '<br>Read at 👁️: ' + new Date(message.updated_at).toLocaleString();
                            messageDetails.appendChild(readAtMessage);
                        }

                        if (message.sender_id == userId) {
                            const deleteButton = document.createElement('button');
                            deleteButton.textContent = '×';
                            deleteButton.className = 'btn btn-danger btn-sm';
                            deleteButton.style.position = 'absolute';
                            deleteButton.style.top = '1%';
                            deleteButton.style.right = '10%';
                            deleteButton.style.transform = 'translate(50%, -50%)';
                            deleteButton.style.width = '30px';
                            deleteButton.style.height = '30px';
                            deleteButton.style.borderRadius = '50%';
                            deleteButton.addEventListener('click', function(event) {
                                event.preventDefault();
                                const messageId = message.id;
                                if (confirm("Are you sure you want to delete this message?")) {
                                    deleteMessage(messageId);
                                }
                            });
                            messageDetails.style.position = 'relative';
                            messageDetails.appendChild(deleteButton);
                        }

                        listItem.appendChild(messageDetails);
                        messageList.appendChild(listItem);

                        if (message.receiver_id == userId && message.is_read === 0) {
                            markMessageAsRead(message.id);
                        }
                    });

                } else if (document.querySelector('.user-link.active')) {
                    const listItem = document.createElement('li');
                    listItem.className = 'no-messages-found';
                    listItem.textContent = 'No messages found.';
                    messageList.appendChild(listItem);
                }

                if (scrollToLast) {
                    const lastMessageItem = messageList.lastElementChild;
                    if (lastMessageItem) {
                        lastMessageItem.scrollIntoView({ behavior: 'smooth', block: 'end' });
                    }
                }

            } catch (error) {
                console.error('Error fetching messages:', error);
            }
        }

        async function deleteMessage(messageId) {
            try {
                const response = await fetch(`/messages/${messageId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                });
                if (response.ok) {
                    const receiverId = document.getElementById('receiver_id').value;
                    await fetchAndDisplayMessages(receiverId, true);
                } else {
                    console.error('Failed to delete message');
                }
            } catch (error) {
                console.error('Error deleting message:', error);
            }
        }

        async function markMessageAsRead(messageId) {
            try {
                const response = await fetch(`/messages/${messageId}/mark-as-read`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ is_read: 1 })
                });
                if (!response.ok) {
                    console.error('Failed to mark message as read');
                }
            } catch (error) {
                console.error('Error marking message as read:', error);
            }
        }

        async function displayUserProfile(userId) {
            try {
                const response = await fetch(`/user/profile/${userId}`, {
                    headers: {
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                const profileInfo = document.getElementById('profile-info');
                profileInfo.innerHTML = '';

                const profileImage = document.createElement('img');
                profileImage.src = data.photo ? data.photo : '/default-profile.png';
                profileImage.alt = 'Profile Photo';
                profileImage.style.width = '100px';
                profileImage.style.height = '100px';
                profileImage.style.borderRadius = '50%';
                profileInfo.appendChild(profileImage);

                profileInfo.appendChild(document.createElement('br'));

                const nameLabel = document.createElement('strong');
                nameLabel.textContent = 'Name: ';
                profileInfo.appendChild(nameLabel);
                profileInfo.appendChild(document.createTextNode(data.name));
                profileInfo.appendChild(document.createElement('br'));

                const emailLabel = document.createElement('strong');
                emailLabel.textContent = 'Email: ';
                profileInfo.appendChild(emailLabel);
                profileInfo.appendChild(document.createTextNode(data.email));
                profileInfo.appendChild(document.createElement('br'));

                const joinedLabel = document.createElement('strong');
                joinedLabel.textContent = 'Joined: ';
                profileInfo.appendChild(joinedLabel);
                const joinedDate = new Date(data.created_at).toLocaleDateString();
                profileInfo.appendChild(document.createTextNode(joinedDate));

                profileInfo.appendChild(document.createElement('br'));
                const interestsLabel = document.createElement('strong');
                interestsLabel.textContent = 'Interests: ';
                profileInfo.appendChild(interestsLabel);
                profileInfo.appendChild(document.createTextNode(data.interests));

                if (data.products && data.products.length > 0) {
                    profileInfo.appendChild(document.createElement('br'));
                    const productsLabel = document.createElement('strong');
                    productsLabel.textContent = 'Products: ';
                    profileInfo.appendChild(productsLabel);
                    data.products.forEach(product => {
                        const productLink = document.createElement('a');
                        productLink.href = `/products/${product.id}`;
                        productLink.textContent = product.name;
                        productLink.style.marginRight = '5px';
                        profileInfo.appendChild(productLink);
                    });
                } else {
                    console.log('No products available for this user.');
                    profileInfo.appendChild(document.createElement('br'));
                    const noProductsLabel = document.createElement('strong');
                    noProductsLabel.textContent = 'No products available';
                    profileInfo.appendChild(noProductsLabel);
                }
            } catch (error) {
                console.error('Error fetching profile:', error);
            }
        }

        function initializeUserLinks() {
            const userLinks = document.querySelectorAll('.user-link');
            userLinks.forEach(link => {
                link.addEventListener('click', async function(event) {
                    event.preventDefault();
                    document.querySelectorAll('.user-link').forEach(user => user.classList.remove('active'));
                    this.classList.add('active');
                    const userId = this.getAttribute('data-id');
                    document.getElementById('receiver_id').value = userId;
                    await fetchAndDisplayMessages(userId, true);
                    displayUserProfile(userId);
                });
            });
        }

        const searchInput = document.getElementById('search-user');
        searchInput.addEventListener('input', function() {
            filterUsers();
            initializeUserLinks();
        });

        function filterUsers() {
            const searchValue = searchInput.value.trim().toLowerCase();
            const users = document.querySelectorAll('.user-link');
            const userList = document.getElementById('user-list');
            users.forEach(user => {
                const userName = user.querySelector('.user-info h5').textContent.trim().toLowerCase();
                if (userName.includes(searchValue)) {
                    userList.prepend(user);
                    user.style.display = 'block';
                } else {
                    user.style.display = 'none';
                }
            });
        }
    
    });

</script>

@endsection